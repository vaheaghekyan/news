<?php

namespace backend\modules\preroll\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\modules\preroll\models\AdsGeolocationTagCountry]].
 *
 * @see \backend\modules\preroll\models\AdsGeolocationTagCountry
 */
class AdsGeolocationTagCountryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\preroll\models\AdsGeolocationTagCountry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\preroll\models\AdsGeolocationTagCountry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}