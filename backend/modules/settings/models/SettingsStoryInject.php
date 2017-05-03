<?php

namespace backend\modules\settings\models;

use Yii;
use backend\models\Language;
use backend\models\Country;

/**
 * This is the model class for table "settings_story_inject".
 *
 * @property integer $id
 * @property integer $language_id
 * @property string $country
 * @property integer $frequency
 * @property integer $type
 */
class SettingsStoryInject extends \yii\db\ActiveRecord
{
    const NATIVE_SPONSORED=0;
    const HTTPOOL=1;

    const FREQUENCY_DIVERGENCE=3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings_story_inject';
    }

  /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'country_id', 'frequency', 'type'], 'required'],
            [['language_id', 'country_id', 'frequency', 'type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'language_id' => Yii::t('app', 'Language'),
            'country_id' => Yii::t('app', 'Country'),
            'frequency' => Yii::t('app', 'Frequency'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\settings\models\activequery\SettingsStoryInjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\settings\models\activequery\SettingsStoryInjectQuery(get_called_class());
    }

    /*
    *  return types of story inject
    */
    public static function injectType()
    {
        return
        [
            SettingsStoryInject::NATIVE_SPONSORED => 'Native Sponsored',
            SettingsStoryInject::HTTPOOL => 'HTTPool',
        ];
    }

    /*
    * return name of inject type
    * $type - 0,1...
    */
    public static function returnInjectType($type)
    {
        $arr=SettingsStoryInject::injectType();
        return $arr[$type];
    }
}
