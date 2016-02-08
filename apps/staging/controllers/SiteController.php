<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\Story;
use app\models\StoryPriority;
use app\models\StoryImage;

/**
 * Controls the homepage of the site
 */
class SiteController extends Controller
{
    /**
     * Creates behaviors the site should follow
     *
     * Access Rule 1: Only authenticated users can view this page, otherwise they will be forced to login
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Creates the actions that the site should used in certain cases
     *
     * Error: If there is an error, delegate it to the ErrorAction page class
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /** 
     * Creates the homepage
     *
     * Gathers the list of stories and sorts them by size, after this it displays the stories to the page
     */
    public function actionIndex()
    {
        // Find main story, if there is one
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
            ->all();
        $story_list = $this->formatStoryList($story_list);
        if(count($story_list) > 0 || count($main_story) > 0){
            return $this->render('index.twig', ['story_list' => $story_list, 'main_story' => $main_story]);
        } else {
            return $this->render('index.twig');
        }
    }

    /**
     * Formats the story list after sorting it, prepares it for display
     */
    public function formatStoryList($story_list){
        // BEGIN: sorting out the featured stories from the side stories
        $featured_list_h = [];
        $featured_list_v = [];
        $side_list = [];
        $maxStories = count($story_list);
        for($i = 0; $i < $maxStories; $i++){
            if($story_list[$i]['story_size'] == 2 || $story_list[$i]['story_size'] == 5){
                $featured_list_h[] = $story_list[$i];
            } else if($story_list[$i]['story_size'] == 3){
                $featured_list_v[] = $story_list[$i];
            } else if($story_list[$i]['story_size'] == 4){
                $side_list[] = $story_list[$i];
            }
        }
        // END:  sorting out the featured stories from the side stories

        // BEGIN: sorting out the featured stories from horizontal stories and vertical stories
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
        // END: sorting out the featured stories from horizontal stories and vertical stories

        // put side stories into the story list
        $story_list['side_list'] = $side_list;
        return $story_list;
    }

    /**
     * Create login for the site.
     */
    public function actionLogin()
    {
        // if user is returning to the site and is logged in, go to the homepage
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // if logged in successfully, go back to the url previously accessed
            return $this->goBack();
        } else {
            // if login is a failure, or if the user is first landing on the site (NO POST), redirect to login page
            return $this->render('/login/index.twig', [
                'model' => $model,
            ]);
        }
        
    }

    /**
     * Create logout for the site
     */
    public function actionLogout()
    {
        // logout
        Yii::$app->user->logout();

        // go to home page
        return $this->goHome();
    }
}
