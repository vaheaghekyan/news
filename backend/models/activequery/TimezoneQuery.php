<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\Timezone]].
 *
 * @see \backend\models\Timezone
 */
class TimezoneQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\Timezone[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\Timezone|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}