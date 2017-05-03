<?php

namespace backend\modules\preroll\models;

use Yii;

/**
 * This is the model class for table "{{%ads_geolocation_tags}}".
 *
 * @property integer $tagId
 * @property string $tagName
 * @property string $tagUrl
 *
 * @property AdsGeolocationTagCountry[] $adsGeolocationTagCountries
 */
class AdsGeolocationTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ads_geolocation_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
        [
            [['tagName', 'tagUrl'], 'required'],
            [['tagUrl'], 'string'],
            [['tagName'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tagId' => Yii::t('app', 'Tag ID'),
            'tagName' => Yii::t('app', 'Tag Name'),
            'tagUrl' => Yii::t('app', 'Tag Url'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdsGeolocationTagCountries()
    {
        return $this->hasMany(AdsGeolocationTagCountry::className(), ['tagId' => 'tagId']);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\preroll\models\activequery\AdsGeolocationTagsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\preroll\models\activequery\AdsGeolocationTagsQuery(get_called_class());
    }
}
