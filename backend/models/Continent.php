<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "continents".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $order_index
 *
 * @property Countries[] $countries
 */
class Continent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'continents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_index'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => 'Name',
            'code'          => 'Code',
            'order_index'   => 'Order Index',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountries()
    {
        return $this->hasMany(Country::className(), ['continent_id' => 'id']);
    }

    public function getCountriesByContinent()
    {

        $languageId = Language::getCurrentId();
        return Country::find()->where(array("continent_id" => $this->id))
            ->andWhere(array("language_id" => $languageId))
            ->orderBy("order_index ASC")
            ->all();

    }

    public static function changePlaces( $ids)
    {

        if ( $ids && ( $ids = explode(",", $ids) ) && count($ids) ) {

            foreach ( $ids as $key => $value ) {

                $model              = self::findOne( trim( $value ) );
                $model->order_index = $key + 1;
                $model->save(false, array("order_index"));

            }

        }

    }
}
