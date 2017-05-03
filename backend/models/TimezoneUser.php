<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%timezone_user}}".
 *
 * @property integer $id
 * @property integer $timezone_id
 * @property integer $user_id
 *
 * @property Timezone $timezone
 * @property Users $user
 */
class TimezoneUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%timezone_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezone_id', 'user_id'], 'required'],
            [['timezone_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'timezone_id' => Yii::t('app', 'Timezone'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationTimezone()
    {
        return $this->hasOne(Timezone::className(), ['id' => 'timezone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\TimezoneUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\TimezoneUserQuery(get_called_class());
    }
}
