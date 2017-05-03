<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\Story3rdPartyVideo]].
 *
 * @see \backend\models\Story3rdPartyVideo
 */
class Story3rdPartyVideoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\Story3rdPartyVideo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\Story3rdPartyVideo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}