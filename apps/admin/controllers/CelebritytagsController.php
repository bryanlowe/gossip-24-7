<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\StoryTag;
use app\models\StoryTagList;

class CelebritytagsController extends Controller
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
        // create story tag list
        $tag_list = StoryTag::find()
            ->select('story_tag_id, tag_name')
            ->orderBy('tag_name ASC')
            ->asArray()
            ->all();
        // apply story tag list to the view
        if(count($tag_list)){
            return $this->render('index.twig', ['tag_list' => $tag_list]);
        } else {
            return $this->render('index.twig');
        }
    }

    public function actionTaglist() {
        // create story tag list
        $tag_list = StoryTag::find()
            ->select('story_tag_id, tag_name')
            ->orderBy('tag_name ASC')
            ->asArray()
            ->all();
        // apply story tag list to the view
        if(count($tag_list)){
            return $this->renderPartial('edit_tag_fragment.twig', ['tag_list' => $tag_list]);
        } else {
            return $this->renderPartial('edit_tag_fragment.twig');
        }
    }

    /**
     * Loads a story tag by its id
     */
    public function actionLoad(){
        $story_values = Yii::$app->request->post('story_values');
        $model = StoryTag::findOne($story_values['story_tag_id']);
        echo json_encode($model->attributes);
    }

    /**
     * Saves the new story tag entry to the database and echos the result
     */
    public function actionSave() {
        $story_values = Yii::$app->request->post('story_values');
        $model = new StoryTag;
        $model->setScenario(StoryTag::SCENARIO_STORY_TAG);
        $model->attributes = $story_values;

        // maintain uniqueness between tags
        $tag_list = StoryTag::find()
            ->select('story_tag_id, tag_name')
            ->where('UPPER(tag_name) = "' . strtoupper($story_values['tag_name']) . '"')
            ->asArray()
            ->all();

        if(count($tag_list) == 0){
            if(isset($story_values['story_tag_id'])){
                echo json_encode(['save_success' => $model->saveStoryTag(), 'errors' => $model->getErrors()]);
            } else {
                echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
            }
        } else {
            echo json_encode(['save_success' => false, 'errors' => ['tag_name' => ['Tag \'' . $story_values['tag_name'] . '\' already exists']]]);
        }
    }

    /**
     * Deletes a story tag from the database and echos the result
     */
    public function actionDelete() {
        $story_values = Yii::$app->request->post('story_values');
        if(is_numeric($story_values['story_tag_id'])){
            $model = StoryTag::findOne($story_values['story_tag_id']);
            $result = $model->delete();
            $errors = $model->getErrors();
            if($result){
                StoryTagList::deleteAll(['story_tag_id' => $story_values['story_tag_id']]);
            }
            echo json_encode(['save_success' => $result, 'errors' => $errors]);
        }
        
    }
}
