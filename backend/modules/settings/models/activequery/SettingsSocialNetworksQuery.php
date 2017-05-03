<?php

namespace backend\modules\settings\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\modules\settings\models\SettingsSocialNetworks]].
 *
 * @see \backend\modules\settings\models\SettingsSocialNetworks
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
     * @return \backend\modules\settings\models\SettingsSocialNetworks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\settings\models\SettingsSocialNetworks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}