<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\LanguagesAll]].
 *
 * @see \backend\models\LanguagesAll
 */
class LanguagesAllQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\LanguagesAll[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\LanguagesAll|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}