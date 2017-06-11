<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\SiteConfig;
use app\models\Story;
use app\models\StoryPriority;
use app\models\StoryImage;
use app\models\StoryTag;
use app\models\StoryTagList;

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
        $story = $this->attachTags($story, true);

        // attaches audio assets to main story
        $story = $this->attachAudio($story, true);

        // attaches video assets to main story
        $story = $this->attachVideo($story, true);

        // create story list
        $story_list = Story::find()
            ->select('story.*')
            ->innerJoinWith('storyPriority')
            ->joinWith('storyImage')
            ->where('story.story_id <> '.$story_id)
            ->andWhere(['visible' => 1])
            ->orderBy('priority ASC')
            ->asArray()
            ->all();
        $story_list = $this->attachTags($story_list);

        // attaches audio assets to each story
        $story_list = $this->attachAudio($story_list);

        // attaches video assets to each story
        $story_list = $this->attachVideo($story_list);

        $story_list = $this->formatStoryList($story_list);
        return $this->render('index.twig', ['story' => $story, 'side_stories' => $story_list, 'currentYear' => date(Y)]);
    }

    /**
     * Adds the story tags to each story
     */
    private function attachTags($story_list, $main_story = false){
        if(!$main_story){
            if(($maxStories = count($story_list)) > 0){
                for($i = 0; $i < $maxStories; $i++){
                    $story_tag_id_list = StoryTagList::findAll(['story_id' => $story_list[$i]['story_id']]);
                    if(($maxStoryTags = count($story_tag_id_list)) > 0){
                        /**************COLLECT STORY TAGS ASSOCIATED WITH THIS STORY - START***************/
                        $story_tag_id_set = array();
                        for($j = 0; $j < $maxStoryTags; $j++){
                            $story_tag_id_set[] = $story_tag_id_list[$j]['story_tag_id'];
                        }
                        $story_list[$i]['tags'] = StoryTag::find()
                            ->select('*')
                            ->where(['in', 'story_tag_id', $story_tag_id_set])
                            ->orderBy('tag_name ASC')
                            ->asArray()
                            ->all();
                        /**************COLLECT STORY TAGS ASSOCIATED WITH THIS STORY - END***************/
                    } else {
                        $story_list[$i]['tags'] = array();    
                    }
                }
            }
        } else {
            $story_tag_id_list = StoryTagList::findAll(['story_id' => $story_list['story_id']]);
            if(($maxStoryTags = count($story_tag_id_list)) > 0){
                /**************COLLECT STORY TAGS ASSOCIATED WITH THIS STORY - START***************/
                $story_tag_id_set = array();
                for($j = 0; $j < $maxStoryTags; $j++){
                    $story_tag_id_set[] = $story_tag_id_list[$j]['story_tag_id'];
                }
                $story_list['tags'] = StoryTag::find()
                    ->select('*')
                    ->where(['in', 'story_tag_id', $story_tag_id_set])
                    ->orderBy('tag_name ASC')
                    ->asArray()
                    ->all();
                /**************COLLECT STORY TAGS ASSOCIATED WITH THIS STORY - END***************/
            } else {
                $story_list['tags'] = array();    
            }
        }
        return $story_list;
    }

    /**
     * Adds the story audio to each story
     */
    private function attachAudio($story_list, $main_story = false){
        if(!$main_story){
            if(($maxStories = count($story_list)) > 0){
                for($i = 0; $i < $maxStories; $i++){
                    $story_list[$i]['audio'] = Yii::$app->runAction('audio/assets', ['id' => $story_list[$i]['story_id']]);
                }
            }
        } else {
            $story_list['audio'] = Yii::$app->runAction('audio/assets', ['id' => $story_list['story_id']]);
        }
        return $story_list;
    }

    /**
     * Adds the story video to each story
     */
    private function attachVideo($story_list, $main_story = false){
        if(!$main_story){
            if(($maxStories = count($story_list)) > 0){
                for($i = 0; $i < $maxStories; $i++){
                    $story_video = Yii::$app->runAction('video/assets', ['id' => $story_list[$i]['story_id']]);
                    if(count($story_video) > 0){
                        $story_list[$i]['video'] = $story_video[0];
                    }
                }
            }
        } else {
            $story_video = Yii::$app->runAction('video/assets', ['id' => $story_list['story_id']]);
            if(count($story_video) > 0){
                $story_list['video'] = $story_video[0];
            }
        }
        return $story_list;
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
            if($i % 2 == 0){
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
}
