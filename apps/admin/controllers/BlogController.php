<?php
namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Story;

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
        $model = new Story(['scenario' => Story::SCENARIO_STORY]);
        $newStory = Yii::$app->request->post('story_values');
        $newStory['story_date'] = date('r');
        $model->attributes = $newStory;
        if($model->validate()){
            echo json_encode(['save_success' => $model->saveStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }
}
