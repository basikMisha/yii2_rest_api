<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;
class Token extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%token}}';
    }

    public static function generateToken($userId)
    {
        $token = Yii::$app->security->generateRandomString();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $model = new static();
        $model->user_id = $userId;
        $model->token = $token;
        $model->expires_at = $expiresAt;
        $model->save();

        return $model->token;
    }

    public static function validateToken($token)
    {
        return static::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->one();
    }
}
