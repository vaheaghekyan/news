<?php

namespace backend\models;

use Yii;
use backend\models\CountryExt;

/**
 * This is the model class for table "countries_ext".
 *
 * @property string $code
 * @property string $name
 * @property string $native
 * @property string $phone
 * @property string $continent
 * @property string $capital
 * @property string $currency
 * @property string $languages
 */
class CountryExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries_ext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code', 'continent'], 'string', 'max' => 2],
            [['name', 'native', 'capital'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 15],
            [['currency', 'languages'], 'string', 'max' => 30],
            [['code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'native' => 'Native',
            'phone' => 'Phone',
            'continent' => 'Continent',
            'capital' => 'Capital',
            'currency' => 'Currency',
            'languages' => 'Languages',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountry()
    {
        return $this->hasOne(Country::className(), ['name' => 'name']);
    }
}
