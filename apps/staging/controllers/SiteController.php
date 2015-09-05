<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Story;
use app\models\StoryPriority;
use app\models\StoryImage;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $main_story = Story::find()
            ->select('story.*')
            ->where(['story_size' => 1, 'visible' => 1])
            ->joinWith('storyImage')
            ->asArray()
            ->limit(1)
            ->one();

        // create story list
        $story_list = Story::find()
            ->select('story.*')
            ->innerJoinWith('storyPriority')
            ->joinWith('storyImage')
            ->where('story_size <> 1')
            ->andWhere(['visible' => 1])
            ->orderBy('priority ASC')
            ->asArray()
            ->limit(29)
            ->all();
        $story_list = $this->formatStoryList($story_list);
        if(count($story_list) > 0 || count($main_story) > 0){
            return $this->render('index.twig', ['story_list' => $story_list, 'main_story' => $main_story]);
        } else {
            return $this->render('index.twig');
        }
    }

    public function formatStoryList($story_list){
        $featured_list_h = [];
        $featured_list_v = [];
        $side_list = [];
        $maxStories = count($story_list);
        for($i = 0; $i < $maxStories; $i++){
            if($story_list[$i]['story_size'] == 2){
                $featured_list_h[] = $story_list[$i];
            } else if($story_list[$i]['story_size'] == 3){
                $featured_list_v[] = $story_list[$i];
            } else if($story_list[$i]['story_size'] == 4){
                $side_list[] = $story_list[$i];
            }
        }
        $story_list = [];
        $story_list['featured_list'] = [];
        $storyIndex = 0;
        $maxFeatured_h = count($featured_list_h);
        $maxFeatured_v = count($featured_list_v);
        for($i = 0; $i < $maxFeatured_h; $i++){
            if($i % 2 == 0){
                $storyIndex++;
            }
            if(empty($story_list['featured_list'][$storyIndex]['featured_list_h'])){
                $story_list['featured_list'][$storyIndex]['featured_list_h'] = [];
            }
            $story_list['featured_list'][$storyIndex]['featured_list_h'][] = $featured_list_h[$i];
        }
        $storyIndex = 0;
        for($i = 0; $i < $maxFeatured_v; $i++){
            if($i % 3 == 0){
                $storyIndex++;
            }
            if(empty($story_list['featured_list'][$storyIndex]['featured_list_v'])){
                $story_list['featured_list'][$storyIndex]['featured_list_v'] = [];
            }
            $story_list['featured_list'][$storyIndex]['featured_list_v'][] = $featured_list_v[$i];
        }
        $story_list['side_list'] = $side_list;
        return $story_list;
    }

    public function actionPrototype($id = 1)
    {
        return $this->render('prototype_'.$id);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
