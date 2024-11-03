<?php
namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
class AdminController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        return Yii::$app->user->identity->role === 1;
                    },
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionUsers()
    {
        return User::find()->all();
    }

    public function actionDelete($id)
    {
        $targetUser = User::findOne($id);
        if ($targetUser && $targetUser->delete()) {
            return ['message' => 'Пользователь удален успешно'];
        }
        return ['error' => 'Ошибка при удалении пользователя'];
    }

    public function actionCreate()
    {
    
    Yii::$app->response->format = Response::FORMAT_JSON;

    $user = new User();
    $user->username = Yii::$app->request->post('username');
    $user->email = Yii::$app->request->post('email');
    $user->role = Yii::$app->request->post('role', 0);
    $timestamp = time();
    $mysql_time = date('Y-m-d H:i:s', $timestamp);
    $user->created_at = $mysql_time;
    $user->updated_at = $mysql_time;

    $password = Yii::$app->request->post('password');
    $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    if ($user->save()) {
        return ['message' => 'Пользователь добавлен успешно', 'user' => $user];
    } else {
        return ['error' => 'Ошибка при добавлении пользователя', 'details' => $user->errors];
    }
    }

    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::findOne($id);
        if (!$user) {
            return ['error' => 'Пользователь не найден'];
        }

        $user->username = Yii::$app->request->post('username', $user->username);
        $user->email = Yii::$app->request->post('email', $user->email);
        $user->role = Yii::$app->request->post('role', $user->role);
        $timestamp = time();
        $mysql_time = date('Y-m-d H:i:s', $timestamp);
        $user->updated_at = $mysql_time;
    
        $password = Yii::$app->request->post('password');
        if ($password) {
            $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        }
    
        if ($user->save()) {
            return ['message' => 'Пользователь обновлен успешно', 'user' => $user];
        } else {
            return ['error' => 'Ошибка при обновлении пользователя', 'details' => $user->errors];
        }
    }
}
