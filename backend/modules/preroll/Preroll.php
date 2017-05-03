<?php

namespace backend\modules\preroll;

class Preroll extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\preroll\controllers';

    const TABLE_IP_COUNTRIES = "ads_geolocation_ip_countries";
    const TABLE_COUNTRIES = "ads_geolocation_countries";
    const TABLE_TAGS = "ads_geolocation_tags";
    const TABLE_TAG_COUNTRY = "ads_geolocation_tag_country";

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
