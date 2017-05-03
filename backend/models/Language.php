<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use backend\models\UserLanguage;
use backend\models\Language;
use backend\components\Helpers;

use yii\caching\DbDependency;

/**
 * This is the model class for table "languages".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 *
 * @property Countries[] $countries
 * @property Stories[] $stories
 */
class Language extends \yii\db\ActiveRecord
{
    const DEFAULT_LANGUAGE  = "en";
    const COOKIE_KEY        = "backend_language";

    private static $currentId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 45],
            [['code'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Countries::className(), ['language_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Stories::className(), ['language_id' => 'id']);
    }

   /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryLanguage()
    {
        return $this->hasMany(CountryLanguage::className(), ['language_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationUserLanguage()
    {
        return $this->hasMany(UserLanguage::className(), ['language_id' => 'id']);
    }

    /**
     * @param $code - en,hr...
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByCode( $code )
    {
        return self::find()->where(array("code" => $code))->one();
    }

    /**
     * @param $id is id in languages.
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findById( $id )
    {
        $result=self::findOne($id);

        if(!empty($result))
            return $result;
        else
            return self::findOne(7); //find english
    }

    /**
     * This returns current language code, for example: en, hr...
     * @return mixed|string
     */
    public static function getCurrent()
    {
        $language = self::DEFAULT_LANGUAGE;
        if ( Yii::$app->session->has( self::COOKIE_KEY ) )
        {

            $language = Yii::$app->session->get(Language::COOKIE_KEY);

        }
        else if( isset( Yii::$app->request->cookies[ Language::COOKIE_KEY ] ) )
        {

            $language = Yii::$app->request->cookies[Language::COOKIE_KEY]->value;

        }

        return $language;
    }

    /**
     * This returns current language "id" from langauge database
     * @return mixed
     */
    public static function getCurrentId()
    {
        if ( !self::$currentId ) {

            $languageCode = Language::getCurrent();
            $language = Language::find()->where(array("code" => $languageCode))->one();
            self::$currentId = $language->id;

        }
        return self::$currentId;
    }

    /**
     * This returns current language object
     * @return null|static
     */
    public static function getCurrentLanguage()
    {

        return self::findOne( self::getCurrentId() );

    }

    /**
     * @param $code - forexample: en, hr...
     */
    public static function setLanguage( $code )
    {
        Yii::$app->session->set( self::COOKIE_KEY, $code );
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => self::COOKIE_KEY,
            'value' => $code,
            'expire' => time() + (60*60*24*365*10) //current time + 10 years
        ]));
    }

    /*
    *  get dropdown list of all active languages
    * $nativeLangauge -
        true = return langauge name in their native language, so Croatian is Hrvatski, Spanish is Espanol...
        false = return all in English
    */
    public static function dropDownActiveLanguages($nativeLangauge=false)
    {
        $lang = Yii::$app->language;
        if($nativeLangauge==true)
            Yii::$app->language="en";

        $tableLanguage=Language::tableName();
        $dependency=new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableLanguage";
        $query=Language::getDb()->cache(function($db)
        {
            //dont show swedish and uzbek
            return Language::find()->orderBy('name ASC')->where("id<>9 AND id<>25")->all();

        }, Yii::$app->params['7_day_cache'], $dependency);

        if($nativeLangauge==true)
        {
            $return = ArrayHelper::map($query, 'id', function($query)
            {
                return Yii::t('app', $query->name)." ($query->name)";
            });
            //set old language back, because of modal popup on frontend
            Yii::$app->language = $lang;
            return $return;
        }
        else
            return ArrayHelper::map($query, 'id', 'name');
    }

    /*
    *  get messages directory
    * $country_code - en, hr...
    */
    public static function messageDir($country_code)
    {
        return Yii::getAlias("@backend")."/messages/$country_code/";
    }

    /*
    *  get messages directory
    * $country_code - en, hr...
    */
    public static function frontendMessageDir($country_code)
    {
        return Yii::getAlias("@frontend")."/messages/$country_code/";
    }

    /*
    *  get languages for specific user.
    * used in side-overlay to fill dropdown menu
    */
    public static function userRelatedLanguages()
    {
        $dependency= new DbDependency;
        $cache=Helpers::cache();
        $role=Yii::$app->user->getIdentity()->role;
        if ( $role == User::ROLE_SUPERADMIN || $role == User::ROLE_ADMIN  || $role == User::ROLE_MARKETER  )
        {
            $languageTable=Language::tableName();
            $dependency->sql="SELECT MAX(id) FROM $languageTable";
            $languages=Language::getDb()->cache(function($db)
            {
                return Language::find()->orderBy('name ASC')->all();
            }, \Yii::$app->params['14_day_cache'], $dependency);

        }
        else
        {
            $IDuser=Yii::$app->user->getId();
            $tableUserLanguage=UserLanguage::tableName();
            $dependency->sql="SELECT MAX(id) FROM $tableUserLanguage";
            $cache_key="user_related_lang_".$IDuser;
            // try retrieving $data from cache
            $languages = $cache->get($cache_key);

            if ($languages === false)
            {
                $tableUserLanguage=UserLanguage::tableName();
                $languages = Language::find()
                ->joinWith(['relationUserLanguage'])
                ->where(["$tableUserLanguage.user_id"=>$IDuser])
                ->orderBy('name ASC')
                ->all();
                $cache->set($cache_key, $languages, \Yii::$app->params['14_day_cache'], $dependency);
            }

        }

        return $languages;
    }

}
