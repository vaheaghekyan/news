<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%timezone}}".
 *
 * @property integer $id
 * @property string $timezone
 */
class Timezone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%timezone}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezone'], 'required'],
            [['timezone'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'timezone' => Yii::t('app', 'Timezone'),
        ];
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\TimezoneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\TimezoneQuery(get_called_class());
    }

    /*
    *  generate data for dropdown list of all timezones
    */
    public static function dropDownListTimezone()
    {
        $result=Timezone::find()->orderBy('timezone ASC')->all();
        return ArrayHelper::map($result, 'id', 'timezone');

    }
}
