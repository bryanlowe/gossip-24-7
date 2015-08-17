<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Story;
use app\models\StoryPriority;
use app\models\StoryImage;

class StorylistController extends Controller
{
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

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
                ->limit(30)
                ->all();
            $maxStories = count($story_list);
            if(!empty($filters) && array_key_exists('story_type', $filters)){
                $temp = [];
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['story_type'] == $filters['story_type']){
                        $temp[] = $story_list[$i];
                    }
                }
                $story_list = $temp;
            }

            if(!empty($filters) && array_key_exists('visibility', $filters)){
                $temp = [];
                for($i = 0; $i < $maxStories; $i++){
                    if($story_list[$i]['visible'] == $filters['visibility']){
                        $temp[] = $story_list[$i];
                    }
                }
                $story_list = $temp;
            }
                
            if(count($story_list) > 0){
                return $this->renderPartial('story_list.twig', ['story_list' => $story_list]);
            } else {
                return $this->renderPartial('story_list.twig');
            }
        } else {
            // create story list
            $story_list = Story::find()
                ->select('story.*')
                ->innerJoinWith('storyPriority')
                ->orderBy('priority ASC')
                ->asArray()
                ->limit(30)
                ->all();
            if(count($story_list) > 0){
                return $this->render('index.twig', ['story_list' => $story_list]);
            } else {
                return $this->render('index.twig');
            }
        }
    }

    /**
     * Saves the new story entry to the database and echos the result
     */
    public function actionSave() {
        $story_values = Yii::$app->request->post('story_values');
        if($story_values['story_type'] == 'SPOTLIGHT'){
            $story_list = Story::findAll(['story_type' => $story_values['story_type']]);
            foreach($story_list as $story){
                if($story->story_id != $story_values['story_id']){
                   $story->story_type = 'FEATURED'; 
                   $story->save(true, ['story_type']);
                }
            }
        }
        $model = Story::findOne($story_values['story_id']);
        $model->setScenario(Story::SCENARIO_STORY);
        $model->attributes = $story_values;
        echo json_encode(['save_success' => $model->save(true, ['title','story_type','link','description']), 'errors' => $model->getErrors()]);
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