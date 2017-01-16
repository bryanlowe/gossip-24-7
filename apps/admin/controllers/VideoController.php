<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Story;
use app\models\StoryVideo;

class VideoController extends Controller implements MediaController
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
     * load story assets from the database and echos the result or returns the result as an array
     */
    public function actionAssets($id = "") {
        $model = new StoryVideo;
        $model->setScenario(StoryVideo::SCENARIO_STORY_VIDEO);
        if(Yii::$app->request->isAjax && !is_numeric($id)){
            $story_values = Yii::$app->request->post('story_values');
            // create asset list
            $video = StoryVideo::findOne(['story_id' => $story_values['story_id']]);

            // apply media assets to the view
            if(!empty($video)){
                echo $this->renderPartial('media_assets.twig', ['media_assets' => $video->attributes]);
            } else {
                echo $this->renderPartial('media_assets.twig', ['story_id' => $story_values['story_id']]);
            }
        } else if(is_numeric($id)){
            $video_list = StoryVideo::find()->where(['story_id' => $id])->asArray()->all();
            return $video_list;
        }
    }

    /**
     * Deletes a story video from the database and echos the result
     */
    public function actionDelete() {
        $story_values = Yii::$app->request->post('story_values');
        $model = StoryVideo::findOne($story_values['story_video_id']);
        echo json_encode(['save_success' => $model->delete(), 'errors' => $model->getErrors()]);
    }

    /**
     * Save video information into the database
     */
    public function actionSave() {
        $model = new StoryVideo;
        $model->setScenario(StoryVideo::SCENARIO_STORY_VIDEO);
        if(Yii::$app->request->isAjax){
            // don't add a new entry if there is already an image in database for this story
            $existingVideoEntry = $this->actionAssets(Yii::$app->request->post('story_id'));
            if(empty($existingVideoEntry)){
                $model->attributes = Yii::$app->request->post('story_values');
                if($model->save()){
                    echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
                } else {
                    echo json_encode(['save_success' => 0, 'errors' => $model->getErrors()]);
                }    
            } else {
                echo json_encode(['save_success' => 0, 'errors' => [['Only one video can be added per story. If you want a new video, remove the current video and upload again.']]]);
            }  
        }
    }

    /**
     * uploads files to the targeted folder
     */
    public function actionUpload() {}
}
