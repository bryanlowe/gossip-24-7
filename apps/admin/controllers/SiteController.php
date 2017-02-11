<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\SiteConfig;

class SiteController extends Controller
{
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

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index.twig');
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
            $this->layout = 'login.twig';
            return $this->render('/login/index.twig', [
                'model' => $model,
            ]);
        }
        
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Update the site maintenance toggle to the database and echos the result
     */
    public function actionUpdatemaintenance() {
        $maintenance_toggle = Yii::$app->request->post('maintenance_toggle');
        $model = SiteConfig::findOne($maintenance_toggle['site_config_id']);
        $model->setScenario(SiteConfig::SCENARIO_SITE_CONFIG_MAINTENANCE);
        $model->attributes = $maintenance_toggle;
        echo json_encode(['save_success' => $model->save(true, ['maintenance_mode']), 'errors' => $model->getErrors()]);
    }
}
