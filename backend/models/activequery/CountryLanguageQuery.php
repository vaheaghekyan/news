<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\app\models\CountryLanguage]].
 *
 * @see \app\models\CountryLanguage
 */
class CountryLanguageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\CountryLanguage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\CountryLanguage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}