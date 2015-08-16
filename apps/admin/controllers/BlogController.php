<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Story;
use app\models\StoryPriority;

class BlogController extends Controller
{
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex() {
        return $this->render('index.twig');
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
        $model = new Story;
        $model->setScenario(Story::SCENARIO_STORY);
        $model->attributes = $story_values;
        $result = $model->save();
        $errors = $model->getErrors();
        if($result){
            $model->link = 'http://www.gossip247.com/blog/'.$model->getPrimaryKey();
            $model->save();
            $storyPriority = new StoryPriority;
            $storyPriority->setScenario(StoryPriority::SCENARIO_STORY_PRIORITY);
            $storyPriority->story_id = $model->getPrimaryKey();
            $storyPriority->priority = 30;
            $result = $storyPriority->save(true, ['story_id', 'priority']);
            $errors = $storyPriority->getErrors();
        }
        echo json_encode(['save_success' => $result, 'errors' => $errors]);
    }
}
