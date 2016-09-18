<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\Story;
use app\models\StoryAudio;

class AudioController extends Controller implements MediaController
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
        $model = new StoryAudio;
        $model->setScenario(StoryAudio::SCENARIO_STORY_AUDIO);
        if(Yii::$app->request->isAjax && !is_numeric($id)){
            $story_values = Yii::$app->request->post('story_values');
            // create asset list
            $media_assets = [];
            $media_assets['story_id'] = $story_values['story_id'];
            $audio_list = StoryAudio::find()->where(['story_id' => $story_values['story_id']])->asArray()->all();
            $media_assets['audio'] = $audio_list;

            // apply media assets to the view
            if((count($audio_list)) > 0){
                echo $this->renderPartial('media_assets.twig', ['media_assets' => $media_assets, 'model' => $model]);
            } else {
                echo $this->renderPartial('media_assets.twig', ['model' => $model]);
            }
        } else if(is_numeric($id)){
            $audio_list = StoryAudio::find()->where(['story_id' => $id])->asArray()->all();
            return $audio_list;
        }
    }

    /**
     * Deletes a story audio from the database and echos the result
     */
    public function actionDelete() {
        $story_values = Yii::$app->request->post('story_values');
        $model = StoryAudio::findOne($story_values['story_audio_id']);
        $result = $model->delete();
        $errors = $model->getErrors();
        unlink('/home/gossip24/public_html/uploads/story/' . $model->story_id . '/audio/'.$model->audio_name);
        echo json_encode(['save_success' => $result, 'errors' => $errors]);
    }

    /**
     * uploads files to the targeted folder
     */
    public function actionUpload() {
        $model = new StoryAudio;
        $model->setScenario(StoryAudio::SCENARIO_STORY_AUDIO);
        if(Yii::$app->request->isAjax){
            $this->createActiveFormUploadInput("StoryAudio", "audioFile");
            $model->audioFile = UploadedFile::getInstanceByName('StoryAudio[audioFile]');
            $model->story_id = Yii::$app->request->post('story_id');
            $model->audio_name = $model->audioFile->baseName.'.'.$model->audioFile->extension;

            // don't add a new entry if there is already an image in database for this story
            $existingImageEntry = $this->actionAssets($model->story_id);
            if(empty($existingImageEntry)){
                if($model->upload()){
                    echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
                } else {
                    echo json_encode(['save_success' => 0, 'errors' => $model->getErrors()]);
                }    
            } else {
                echo json_encode(['save_success' => 0, 'errors' => [['Only one image can be added per story. If you want a new image, remove the current image and upload again.']]]);
            }  
        }
    }

    /** 
     * Mimics ActiveForm upload input from $_FILE if not empty
     * modelName example: StoryAudio
     * attr example: imageName
     */
    private function createActiveFormUploadInput($modelName, $attr){
        $result = [];
        if(($maxFiles = count($_FILES)) > 0){
            for($i = 0; $i < $maxFiles; $i++){
                foreach($_FILES[$i] as $key => $value){
                    $result[$key][$attr] = $value;
                }
            }
        }
        $_FILES[$modelName] = $result;
    }
}
