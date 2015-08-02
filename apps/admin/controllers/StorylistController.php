<?php
namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Story;
use app\models\StoryPriority;

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
        $model = new Story();
        $story_list = $model->loadStories();
        if(count($story_list) > 0){
            return $this->render('index.twig', ['story_list' => $story_list]);
        } else {
            return $this->render('index.twig');
        }
    }

    /**
     * Saves the new story entry to the database and echos the result
     */
    public function actionSave() {
        $model = new Story(['scenario' => Story::SCENARIO_STORY]);    
        $model->attributes = Yii::$app->request->post('story_values');
        if($model->validate()){
            echo json_encode(['save_success' => $model->saveStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }

    /**
     * Deletes a story from the database and echos the result
     */
    public function actionDelete() {
        $model = new Story(['scenario' => Story::SCENARIO_STORY_ID]);    
        $model->attributes = Yii::$app->request->post('story_values');
        if($model->validate()){
            echo json_encode(['save_success' => $model->deleteStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }

    /**
     * Saves the new story entry to the database and echos the result
     */
    public function actionTogglevisible() {
        $model = new Story(['scenario' => Story::SCENARIO_STORY_VISIBILITY]);
        $model->attributes = Yii::$app->request->post('story_values');
        if($model->validate()){
            echo json_encode(['save_success' => $model->saveStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }

    /**
     * Saves the new story priority entry to the database and echos the result
     */
    public function actionPriority() {
        $model = new StoryPriority(['scenario' => Story::SCENARIO_STORY_PRIORITY]);
        $model->attributes = Yii::$app->request->post('story_values');
        if($model->validate()){
            echo json_encode(['save_success' => $model->saveStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }
}
