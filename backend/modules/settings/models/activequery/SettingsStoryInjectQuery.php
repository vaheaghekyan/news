<?php

namespace backend\modules\settings\models\activequery;

/**
 * This is the ActiveQuery class for [[\backend\modules\settings\models\SettingsStoryInject]].
 *
 * @see \backend\modules\settings\models\SettingsStoryInject
 */
class SettingsStoryInjectQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\settings\models\SettingsStoryInject[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\settings\models\SettingsStoryInject|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}