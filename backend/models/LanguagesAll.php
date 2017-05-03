<?php

namespace backend\models;

use Yii;
use backend\models\Language;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "languages_all".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 */
class LanguagesAll extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'languages_all';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\LanguagesAllQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\LanguagesAllQuery(get_called_class());
    }

    /*
    *  find all languages which are not in table "languages", in other words find new lnaguages
    */
    public static function dropDownNewLanguages()
    {
        $tableLanguages=Language::tableName();
        $tableLanguagesAll=LanguagesAll::tableName();
        /*
        SELECT * FROM `languages_all`
        LEFT JOIN languages ON (languages.name=languages_all.name)
        WHERE languages.name IS NULL
        */
        $query=LanguagesAll::find()
        ->leftJoin($tableLanguages, "$tableLanguages.name=$tableLanguagesAll.name")
        ->where("$tableLanguages.name IS NULL")
        ->all();

        return ArrayHelper::map($query, "code", "name");
    }
}
