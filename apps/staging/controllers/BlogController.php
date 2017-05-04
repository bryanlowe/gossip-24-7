<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Story;

class BlogController extends Controller
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

    public function actionIndex($story_id) {
        // create story 
        $story = Story::find()
            ->select('story.*')
            ->joinWith('storyImage')
            ->where(['story.story_id' => $story_id])
            ->asArray()
            ->one();

        // create story list
        $story_list = Story::find()
            ->select('story.*')
            ->innerJoinWith('storyPriority')
            ->joinWith('storyImage')
            ->where('story_size <> '.$story_id)
            ->andWhere(['visible' => 1])
            ->orderBy('priority ASC')
            ->asArray()
            ->all();
        return $this->render('index.twig', ['story' => $story, 'side_stories' => $story_list, 'currentYear' => date(Y)]);
    }
}
