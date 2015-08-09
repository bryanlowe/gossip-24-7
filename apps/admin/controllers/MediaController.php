<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Story;
use app\models\StoryImage;

class MediaController extends Controller
{
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex() {
        // create story list
        $story_list = Story::find()
            ->select('story_id, title')
            ->orderBy('story_id DESC')
            ->asArray()
            ->limit(30)
            ->all();
        // apply story list to the view
        if(count($story_list)){
            return $this->render('index.twig', ['story_list' => $story_list]);
        } else {
            return $this->render('index.twig');
        }
    }

    /**
     * load story assets from the database and echos the result
     */
    public function actionAssets() {
        $model = new StoryImage;
        $model->setScenario(StoryImage::SCENARIO_STORY_IMAGE);
        $story_values = Yii::$app->request->post('story_values');
        // create asset list
        $media_assets = [];
        $media_assets['story_id'] = $story_values['story_id'];
        $image_list = StoryImage::findAll(['story_id' => $story_values['story_id']]);
        $media_assets['images'] = $image_list;

        // apply media assets to the view
        echo $this->renderPartial('media_assets.twig', ['media_assets' => $media_assets, 'model' => $model]);
    }

    /**
     * Deletes a story image from the database and echos the result
     */
    public function actionDelete() {
        $story_values = Yii::$app->request->post('story_values');
        $model = StoryImage::findOne($story_values['story_image_id']);
        $result = $model->delete();
        $errors = $model->getErrors();
        unlink('/home/gossip24/public_html/uploads/story/' . $model->story_id . '/images/'.$model->image_name);
        echo json_encode(['save_success' => $result, 'errors' => $errors]);
    }

    /**
     * uploads files to the targeted folder
     */
    public function actionUpload() {
        $model = new StoryImage;
        $model->setScenario(StoryImage::SCENARIO_STORY_IMAGE);
        if(Yii::$app->request->isAjax){
            $this->createActiveFormUploadInput("StoryImage", "imageFile");
            $model->imageFile = UploadedFile::getInstanceByName('StoryImage[imageFile]');
            $model->story_id = Yii::$app->request->post('story_id');
            $model->image_name = $model->imageFile->baseName.'.'.$model->imageFile->extension;
            if($model->upload()){
                echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
            } else {
                echo json_encode(['save_success' => 0, 'errors' => $model->getErrors()]);
            }
        }
    }

    /** 
     * Mimics ActiveForm upload input from $_FILE if not empty
     */
    public function createActiveFormUploadInput($modelName, $attr){
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
