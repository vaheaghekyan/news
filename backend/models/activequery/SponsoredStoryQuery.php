<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\SponsoredStory]].
 *
 * @see \backend\models\SponsoredStory
 */
class SponsoredStoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\SponsoredStory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\SponsoredStory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}