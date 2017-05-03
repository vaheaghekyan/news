<?php

namespace backend\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\models\Settings]].
 *
 * @see \backend\models\Settings
 */
class SettingsSocialNetworksQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\models\Settings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\models\Settings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}