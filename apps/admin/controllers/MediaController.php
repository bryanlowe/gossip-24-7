<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\Story;
use app\models\StoryImage;

class MediaController extends Controller
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

    public function actionIndex() {
        // create story list
        $story_list = Story::find()
            ->select('story_id, title')
            ->orderBy('story_id DESC')
            ->asArray()
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
        $image_list = StoryImage::find()->where(['story_id' => $story_values['story_id']])->orderBy('order ASC')->asArray()->all();
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
     * Updates the image order to the database and echos the result
     */
    public function actionOrder() {
        $story_values = Yii::$app->request->post('story_values');
        if(($maxStories = count($story_values)) > 0){
            $save_success = 0;
            $errors = [];
            $i = 0;
            do{
                $model = StoryImage::findOne($story_values[$i]['story_image_id']);
                $model->setScenario(StoryImage::SCENARIO_STORY_IMAGE_ORDER);
                if($model->order != $story_values[$i]['order']){
                    $model->order = $story_values[$i]['order'];
                    $save_success = $model->save(true, ['order']);
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
            $model->order = 0;
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
