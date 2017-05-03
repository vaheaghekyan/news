<?php

namespace backend\models;

use Yii;
use backend\models\Language;
use backend\models\CategoryStory;
use backend\models\CategoryLevelOne;
use yii\helpers\ArrayHelper;
use backend\models\CategoriesLevelOne;
use yii\caching\DbDependency;


class Statistics
{
    const TYPE_BY_CATEGORY=0;
    /*
    * all types of statistics are liste here as dropdown list
    */
    public static function statType()
    {
        return
        [
            Statistics::TYPE_BY_CATEGORY=>Yii::t('app', 'By category')
        ];
    }

}
