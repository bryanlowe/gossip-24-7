<?php
namespace app\controllers;
require('../vendor/rss_php/rss_php.php');
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\View;
use app\models\Story;

class RssController extends Controller
{
    // current list of RSS Feeds
    private $rss_feeds = [
        [
            'name' => 'Perez Hilton',
            'url' => 'http://i.perezhilton.com/?feed=rss2'
        ],
        [
            'name' => 'Celebrity-gossip.net',
            'url' => 'http://www.celebrity-gossip.net/site/rss2/'
        ],
        [
            'name' => 'TMZ.com',
            'url' => 'http://www.tmz.com/rss.xml'
        ],
        [
            'name' => 'Gossip Youth',
            'url' => 'http://gossipyouth.com/feed/'
        ],
        [
            'name' => 'Hollywood Gossip',
            'url' => 'http://www.thehollywoodgossip.com/rss.xml'
        ],
        [
            'name' => 'NY Daily News',
            'url' => 'http://feeds.nydailynews.com/nydnrss'
        ],
        [
            'name' => 'Gossip Cop',
            'url' => 'http://www.gossipcop.com/wp-content/plugins/nextgen-gallery-st-1.5.5/xml/media-rss.php'
        ],
        [
            'name' => 'Blind Gossip',
            'url' => 'http://blindgossip.com/?feed=rss2'
        ]
    ];
    
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex() {
        return $this->render('index.twig', [
            'rss_feeds' => $this->rss_feeds
        ]);
    }

    /**
     * loads RSS feeds through RSS URL and echos the result
     */
    public function actionLoad() {
        $url = Yii::$app->request->post('url');
        if($url != 'N/A'){
            $rss = new \rss_php;
            $rss->load($url);
            $items = $rss->getItems(); #returns all rss items
            echo $this->renderPartial('rss_feed_entries.twig', [
                'rss_feed' => $items
            ]); 
        } else {
            echo $this->renderPartial('rss_feed_entries.twig');
        }       
    }

    /**
     * Saves the RSS feed entry to the database and echos the result
     */
    public function actionSave() {
        $model = new Story(['scenario' => Story::SCENARIO_STORY]);
        $model->attributes = Yii::$app->request->post('story_values');
        if($model->validate()){
            echo json_encode(['save_success' => $model->saveStory()]);
        } else {
            echo json_encode(['errors' => $model->errors]);
        }
    }
}
