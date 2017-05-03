<?php

namespace backend\modules\preroll\models;

use Yii;

/**
 * This is the model class for table "{{%ads_geolocation_tag_country}}".
 *
 * @property integer $tagCountry
 * @property integer $tagId
 * @property integer $countryId
 *
 * @property AdsGeolocationCountries $country
 * @property AdsGeolocationTags $tag
 */
class AdsGeolocationTagCountry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ads_geolocation_tag_country}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagId', 'countryId'], 'required'],
            [['tagId', 'countryId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tagCountry' => Yii::t('app', 'Tag Country'),
            'tagId' => Yii::t('app', 'Tag ID'),
            'countryId' => Yii::t('app', 'Country ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(AdsGeolocationCountries::className(), ['idCountry' => 'countryId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(AdsGeolocationTags::className(), ['tagId' => 'tagId']);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\preroll\models\activequery\AdsGeolocationTagCountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\preroll\models\activequery\AdsGeolocationTagCountryQuery(get_called_class());
    }
}
