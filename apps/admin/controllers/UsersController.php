<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;

class UsersController extends Controller
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
        // create user list
        $user_list = User::find()
                ->select('*')
                ->asArray()
                ->all();
        return $this->render('index.twig', [
            'users' => $user_list
        ]);
    }

    /**
     * Saves the new user entry to the database and echos the result
     */
    public function actionSave() {
        $user_values = Yii::$app->request->post('user_values');
        if($user_values['password'] != ""){ $user_values['password'] = Yii::$app->getSecurity()->generatePasswordHash($user_values['password']); }
        if($user_values['user_id'] != ''){
            $model = User::findOne($user_values['user_id']);
            $model->setScenario(User::SCENARIO_USER);
            $model->attributes = $user_values;
            echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
        } else {
            $model = new User;
            $model->setScenario(User::SCENARIO_USER);
            $model->attributes = $user_values;
            echo json_encode(['save_success' => $model->save(), 'errors' => $model->getErrors()]);
        }
    }

    /**
     * Loads a user entry based on it's user id and echos the result
     */
    public function actionLoad() {
        $user_id = Yii::$app->request->post('user_id');
        $user = User::find()->where(['user_id' => $user_id])->asArray()->one();
        if($user){
            echo json_encode(['load_success' => 1, 'user' => $user]);
        } else {
            echo json_encode(['load_success' => 0, 'user' => $user]);
        }
    }
}
