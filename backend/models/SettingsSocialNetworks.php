<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property integer $id
 * @property string $setting_name
 * @property string $setting_code
 */
class SettingsSocialNetworks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['setting_name', 'setting_code'], 'required'],
            [['setting_code'], 'string'],
            [['setting_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'setting_name' => Yii::t('app', 'Setting Name'),
            'setting_code' => Yii::t('app', 'Setting Code'),
        ];
    }

    /**
     * @inheritdoc
     * @return \backend\models\activequery\SettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\activequery\SettingsQuery(get_called_class());
    }

    /*
    *  list of social networking sites
    */
    public static function socialNetworkingSites()
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
        "Intagram"=>"Intagram",
        "Vine"=>"Vine",
        "WhatsApp"=>"WhatsApp",
        "Meetup"=>"Meetup",
        "Viber"=>"Viber"];

        return $array;
    }

    /*
    *  dropdown list of social networking sites
    */
    public static function dropDownSocialNetworkingSites()
    {
        $array=["Twitter", "Facebook", "LinkedIn", "Xing", "Renren", "GooglePlus", "Disqus", "Snapchat", "Tumblr", "Pinterest", "Twoo", "YouTube", "Intagram", "Vine", "WhatsApp", "Meetup","Viber"];

    }
}
