<?php

namespace app\commands;

use yii\console\Controller;
use app\models\User;

class UserController extends Controller
{
    public function actionRegister($username, $email, $password, $role)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password_hash = \Yii::$app->getSecurity()->generatePasswordHash($password);
        if ($role === 'admin') {
            $user->role = 1;
        } else {
            $user->role = 0;
        }
        if ($user->save()) {
            echo "Пользователь зарегистрирован.\n";
        } else {
            echo "Ошибка при регистрации пользователя.\n";
        }
    }
}
