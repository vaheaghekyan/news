<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\SponsoredLevelTwo]].
 *
 * @see \backend\models\SponsoredLevelTwo
 */
class SponsoredLevelTwoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\SponsoredLevelTwo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\SponsoredLevelTwo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}