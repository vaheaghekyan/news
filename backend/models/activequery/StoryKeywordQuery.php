<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\StoryKeyword]].
 *
 * @see \backend\models\StoryKeyword
 */
class StoryKeywordQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\StoryKeyword[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\StoryKeyword|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}