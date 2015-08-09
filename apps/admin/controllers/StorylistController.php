<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
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
        // create story list
        $story_list = Story::find()
            ->select('story.*')
            ->innerJoinWith('storyPriority')
            ->orderBy('story_id DESC')
            ->asArray()
            ->limit(30)
            ->all();
        // apply story list to the view
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
        $story_values = Yii::$app->request->post('story_values');
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
        echo json_encode(['save_success' => $model->save(true, ['visible']), 'errors' => $model->getErrors()]);
    }

    /**
     * Saves the new story priority entry to the database and echos the result
     */
    public function actionPriority() {
        $story_values = Yii::$app->request->post('story_values');
        $model = StoryPriority::findOne($story_values['story_priority_id']);
        $model->setScenario(StoryPriority::SCENARIO_STORY_PRIORITY);
        $model->priority = $story_values['priority'];
        echo json_encode(['save_success' => $model->save(true, ['priority']), 'errors' => $model->getErrors()]);
    }
}
