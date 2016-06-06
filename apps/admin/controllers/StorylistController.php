<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Story;
use app\models\StoryPriority;
use app\models\StoryImage;

class StorylistController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['site/login'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Renders the Story List page
     */
    public function actionIndex() {
        // apply story list to the view
        if(Yii::$app->request->isAjax){
            $filters = (Yii::$app->request->isPost) ? Yii::$app->request->post('filters') : [];
            $orderBy = [];
            if(!empty($filters) && array_key_exists('priority', $filters)){
                $orderBy['priority'] = constant($filters['priority']); 
            } 
            if(!empty($filters) && array_key_exists('story_date', $filters)){
                $orderBy['story_date'] = constant($filters['story_date']); 
            }
            // create story list
            $story_list = Story::find()
                ->select('story.*')
                ->innerJoinWith('storyPriority')
                ->orderBy($orderBy)
                ->asArray()
                ->all();
            $maxStories = count($story_list);

            // filter the stories by size if the story size filter is set
            if(!empty($filters) && array_key_exists('story_size', $filters)){
                $temp = [];
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['story_size'] == $filters['story_size']){
                        $temp[] = $story_list[$i];
                    }
                }
                $story_list = $temp;
            }

            // filter the stories by visibility if the visibility filter is set
            if(!empty($filters) && array_key_exists('visibility', $filters)){
                $temp = [];
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['visible'] == $filters['visibility']){
                        $temp[] = $story_list[$i];
                    }
                }
                $story_list = $temp;
            }

            // attaches image assets to each story
            $story_list = $this->attachImages($story_list);
            $story_image_model = new StoryImage; // used for active web forms
            $story_image_model->setScenario(StoryImage::SCENARIO_STORY_IMAGE);
              
            // render the story list  
            if(($maxStories = count($story_list)) > 0){
                $visible_story_count = 0; // used to show statistics of the count of visible stories
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['visible']){
                       $visible_story_count++; 
                    }
                }
                return $this->renderPartial('story_list.twig', ['story_list' => $story_list, 'story_count' => $maxStories, 'visible_story_count' => $visible_story_count, 'story_image_model' => $story_image_model]);
            } else {
                return $this->renderPartial('story_list.twig');
            }
        } else {
            $orderBy = ['priority' => SORT_ASC, 'story_id' => SORT_DESC];
            // create story list
            $story_list = Story::find()
                ->select('story.*')
                ->innerJoinWith('storyPriority')
                ->orderBy($orderBy)
                ->asArray()
                ->all();

            // attaches image assets to each story
            $story_list = $this->attachImages($story_list);
            $story_image_model = new StoryImage; // used for active web forms
            $story_image_model->setScenario(StoryImage::SCENARIO_STORY_IMAGE);

            //print_r($story_list);

            // render the story list 
            if(($maxStories = count($story_list)) > 0){
                $visible_story_count = 0; // used to show statistics of the count of visible stories
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['visible']){
                       $visible_story_count++; 
                    }
                }
                return $this->render('index.twig', ['story_list' => $story_list, 'story_count' => $maxStories, 'visible_story_count' => $visible_story_count, 'story_image_model' => $story_image_model]);
            } else {
                return $this->render('index.twig');
            }
        }
    }

    /**
     * Adds the story images to each story
     */
    private function attachImages($story_list){
        if(($maxStories = count($story_list)) > 0){
            for($i = 0; $i < $maxStories; $i++){
                $story_list[$i]['images'] = Yii::$app->runAction('media/images', ['story_id' => $story_list[$i]['story_id']]);
            }
        }
        return $story_list;
    }

    /**
     * Saves the new story entry to the database and echos the result
     */
    public function actionSave() {
        $story_values = Yii::$app->request->post('story_values');
        if($story_values['story_size'] == 1){
            $story_list = Story::findAll(['story_size' => $story_values['story_size']]);
            foreach($story_list as $story){
                if($story->story_id != $story_values['story_id']){
                   $story->story_size = 2; 
                   $story->save(true, ['story_size']);
                }
            }
        }
        $model = Story::findOne($story_values['story_id']);
        $model->setScenario(Story::SCENARIO_STORY);
        $model->attributes = $story_values;
        echo json_encode(['save_success' => $model->save(true, ['title','story_size','link','description','show_desc']), 'errors' => $model->getErrors()]);
    }

    /**
     * Deletes a story from the database and echos the result
     */
    public function actionDelete() {
        $story_values = Yii::$app->request->post('story_values');
        $model = Story::findOne($story_values['story_id']);
        $result = $model->delete();
        $errors = $model->getErrors();
        if($result){
            $storyPriority = StoryPriority::findOne(['story_id' => $story_values['story_id']]);
            $result = $storyPriority->delete();
            $errors = $storyPriority->getErrors();

            $image_list = StoryImage::findAll(['story_id' => $story_values['story_id']]);
            foreach($image_list as $image){
                $storyImage = StoryImage::findOne($image['story_image_id']);
                $result = $storyImage->delete();
                $errors = $storyImage->getErrors();
                unlink('/home/gossip24/public_html/uploads/story/' . $storyImage->story_id . '/images/'.$storyImage->image_name);
            }
        }
        echo json_encode(['save_success' => $result, 'errors' => $errors]);
    }

    /**
     * Saves the new story entry to the database and echos the result
     */
    public function actionVisible() {
        $story_values = Yii::$app->request->post('story_values');
        $model = Story::findOne($story_values['story_id']);
        $model->setScenario(Story::SCENARIO_STORY_VISIBILITY);
        $model->visible = $story_values['visible'];
        if($model->story_date == ''){
            $model->story_date = date('U');
            echo json_encode(['save_success' => $model->save(true, ['visible', 'story_date']), 'errors' => $model->getErrors()]);
        } else {
            echo json_encode(['save_success' => $model->save(true, ['visible']), 'errors' => $model->getErrors()]);
        }
    }

    /**
     * Saves the new story priority entry to the database and echos the result
     */
    public function actionPriority() {
        $story_values = Yii::$app->request->post('story_values');
        if(($maxStories = count($story_values)) > 0){
            $save_success = 0;
            $errors = [];
            $i = 0;
            do{
                $model = StoryPriority::findOne($story_values[$i]['story_priority_id']);
                $model->setScenario(StoryPriority::SCENARIO_STORY_PRIORITY);
                if($model->priority != $story_values[$i]['priority']){
                    $model->priority = $story_values[$i]['priority'];
                    $save_success = $model->save(true, ['priority']);
                    $errors = $model->getErrors();
                } else {
                    $save_success = 1;  
                }
                $i++;
            } while($i < $maxStories && $save_success);
            echo json_encode(['save_success' => $save_success, 'errors' => $errors]);
        } else {
            echo json_encode(['save_success' => 1, 'errors' => []]);
        }
    }
}