<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "country_language".
 *
 * @property integer $id
 * @property integer $country_id
 * @property integer $language_id
 *
 * @property Countries $country
 * @property Languages $language
 */
class CountryLanguage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country_language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'language_id'], 'required'],
            [['country_id', 'language_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'language_id' => Yii::t('app', 'Language ID'),
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
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\CountryLanguageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\CountryLanguageQuery(get_called_class());
    }
}
