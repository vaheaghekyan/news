<?php

namespace backend\models;

use Yii;
use backend\models\CountryStory;
use backend\models\Story;
use backend\models\CountryExt;
use backend\models\Continent;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;

/**
 * This is the model class for table "countries".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order_index
 * @property integer $continent_id
 * @property integer $language_id
 *
 * @property Continents $countinent
 * @property Languages $language
 * @property CountryStories[] $countryStories
 * @property Stories[] $stories
 */
//INSERT INTO `countries` (`id`, `name`, `order_index`, `continent_id`, `language_id`) VALUES ('73', 'Worldwide', '50', '1', '8');

class Country extends \yii\db\ActiveRecord
{

    //alywas checked countries, because some countries always has to be checked: "International"
    //also used in Story->returnCountriesForUser() to find and include that "international" country as part of countries, because that country is never included when finding countries by langugage (countries where specific language is spoken), but is included when finding all countries
    public $always_checked=['International'=>53];

    public $countNumberOfStories;//used in StatisticsController

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_index', 'continent_id', 'language_id'], 'integer'],
            [['continent_id', 'language_id'], 'required'],
            [['name'], 'string', 'max' => 150]
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
            'order_index' => 'Order Index',
            'continent_id' => 'Countinent ID',
            'language_id' => 'Language ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountinent()
    {
        return $this->hasOne(Continent::className(), ['id' => 'continent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryStories()
    {
        return $this->hasMany(CountryStory::className(), ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationStories()
    {
        return $this->hasMany(Story::className(), ['id' => 'story_id'])->viaTable('country_stories', ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryLanguages()
    {
        return $this->hasMany(CountryLanguage::className(), ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelationCountryExt()
    {
        return $this->hasOne(CountryExt::className(), ['name' => 'name']);
    }

    /*
    *  check if specific story is in certain conutries, by checking story_id in "country_stories", so you can check checkboxes
    *  you call this function from $country(Country) so it atuomatically binds country_id  with loaded model
    * $storyId - id in "stories"
    */
    public function hasCountry( $storyId )
    {
        return ( $this->getRelationCountryStories()->where(array("story_id" => $storyId))->count() > 0 ? true : false);
    }

    /*
    * Find continent id by providing specific country, because you have to connect continent with country in "countries"
    * $country - country name, e.g. Croatia
    */
    public static function findContinentForCountry($country)
    {
        $tableContinents=Continent::tableName();
        $tableCountryExt=CountryExt::tableName();
        /*
        SELECT id FROM `continents`
        LEFT JOIN countries_ext ON (countries_ext.continent=continents.code)
        WHERE countries_ext.name="Croatia"
        */
        $rows = (new Query())
        ->select(["$tableContinents.id"])
        ->from("$tableContinents")
        ->leftJoin($tableCountryExt, "$tableCountryExt.continent=$tableContinents.code")
        ->where(["$tableCountryExt.name" => $country])
        ->one();

        return $rows['id'];
    }



    /*
    *  find all countries for dropdownlist
    */
    public static function dropDownCountries()
    {
        $query=Country::find()->orderBy('name ASC')->all();
        return ArrayHelper::map($query, "id", "name");
    }

    /*
    * find all countries for dropdownlist but depending on stories that contains specific country
    * in other words only return countries that were selected in stories so you don't list all countries and when you click on country to filter stories, there is no stories written for that country
    * USED IN: filter in story/stories
    */
    public static function dropDownCountriesStoryDepending()
    {
        $tableStory=Story::tableName();
        $tableCountry=Country::tableName();
        $tableCountryStory=CountryStory::tableName();
        $language       = new Language;
        /*
        SELECT countries.* FROM `countries`
        LEFT JOIN country_stories ON country_stories.country_id=countries.id
        LEFT JOIN stories ON stories.id=country_stories.story_id
        WHERE stories.language_id=3
        GROUP BY countries.id
        */
        $rows = (new \yii\db\Query())
        ->select(['countries.*'])
        ->from($tableCountry)
        ->leftJoin($tableCountryStory, "$tableCountryStory.country_id=$tableCountry.id")
        ->leftJoin($tableStory, "$tableStory.id=$tableCountryStory.story_id ")
        ->where(["$tableStory.language_id" => $language->currentId])
        ->groupBy("$tableCountry.id")
        ->orderBy("$tableCountry.name ASC")
        ->all();


        return ArrayHelper::map($rows, "id", "name");
    }

    /*
    * find all countries where specific lanuage(language of current user is) is spoken
    * return also country "International"
    * $languageId for example: 7, 5...
    */
    public static function listCountries($languageId)
    {
        $tableCountry=Country::tableName();

        /*$dependency=new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableCountry";

        $countries  = Country::getDb()->cache(function($db) use($tableCountry, $languageId)
        {
            $Country = new Country;
            $tableCountryLanguage=CountryLanguage::tableName();

            return Country::find()
                    ->joinWith(['relationCountryLanguages'])
                    ->where(["$tableCountryLanguage.language_id"=>$languageId] )
                    ->orWhere(['IN', $tableCountry.'.id', $Country->always_checked]) //for always_checked check in Country.php at the beginning of class
                    ->orderBy('order_index ASC, name ASC')  //order by order_index fist because you want International to be first
                    ->all();
        }, Yii::$app->params['14_day_cache'], $dependency);*/


        $Country = new Country;
        $tableCountryLanguage=CountryLanguage::tableName();

        $countries = Country::find()
                ->joinWith(['relationCountryLanguages'])
                ->where(["$tableCountryLanguage.language_id"=>$languageId] )
                ->orWhere(['IN', $tableCountry.'.id', $Country->always_checked]) //for always_checked check in Country.php at the beginning of class
                ->orderBy('order_index ASC, name ASC')  //order by order_index fist because you want International to be first
                ->all();

        return $countries;
    }

    /*
    * return query result of all countries ordered by order_index and name
    */
    public static function listAllCountries()
    {
        $tableCountry=Country::tableName();

        $dependency=new DbDependency;
        $dependency->sql="SELECT MAX(id) FROM $tableCountry";

        $query=Country::getDb()->cache(function($db)
        {
            return Country::find()->orderBy('order_index ASC, name ASC')->all();
        }, Yii::$app->params['14_day_cache'], $dependency);

        return $query;
    }
}
