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
        return $this->render('index.twig', ['story' => $story]);
    }
}
