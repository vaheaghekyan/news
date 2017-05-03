<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\Stories]].
 *
 * @see \backend\models\Stories
 */
class StoriesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\Stories[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\Stories|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}