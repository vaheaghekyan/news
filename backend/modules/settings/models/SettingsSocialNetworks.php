<?php

namespace backend\modules\settings\models;

use Yii;
use backend\models\Country;
/**
 * This is the model class for table "{{%settings_social_networks}}".
 *
 * @property integer $id
 * @property integer $country_id
 * @property string $social_network
 */
class SettingsSocialNetworks extends \yii\db\ActiveRecord
{
    //for SettingsSocialNetworksSearch, alias
    public $group_concat_social_network_alias;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings_social_networks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'social_network'], 'required'],
            [['country_id'], 'integer'],
            [['social_network'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_id' => Yii::t('app', 'Country'),
            'social_network' => Yii::t('app', 'Social Network'),
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\settings\models\activequery\SettingsSocialNetworksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\settings\models\activequery\SettingsSocialNetworksQuery(get_called_class());
    }

    /*
    *  social networks, array
    */
    public static function socialNetworkSites()
    {
        $array=[
        "Twitter"=>"Twitter",
        "Facebook"=>"Facebook",
        "LinkedIn"=>"LinkedIn",
        "Xing"=>"Xing",
        "Renren"=>"Renren",
        "GooglePlus"=>"GooglePlus",
        "Disqus"=>"Disqus",
        "Snapchat"=>"Snapchat",
        "Tumblr"=>"Tumblr",
        "Pinterest"=>"Pinterest",
        "Twoo"=>"Twoo",
        "YouTube"=>"YouTube",
        "Instagram"=>"Instagram",
        "Vine"=>"Vine",
        "WhatsApp"=>"WhatsApp",
        "Meetup"=>"Meetup",
        "VK"=>"VK"
        ];
        ksort($array);

        return $array;
    }
}
