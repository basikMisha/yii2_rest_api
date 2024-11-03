<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use app\models\User;
use app\models\Token;

class AuthController extends Controller
{
    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            return ['error' => 'Неверное имя пользователя или пароль'];
        }

        $token = Token::generateToken($user->id);

        return ['token' => $token];
    }

    public function actionMe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            
            $tokenRecord = Token::validateToken($token);

            if (!$tokenRecord) {
                return [
                    'error' => 'Пользователь не авторизован',
                ];
            }

            $user = User::findOne($tokenRecord->user_id);

            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ];
        }

        return [
            'error' => 'Пользователь не авторизован',
        ];
    }
}
