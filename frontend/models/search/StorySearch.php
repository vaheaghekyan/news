<?php

namespace frontend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Story;
use backend\models\Language;
use backend\models\CategoryStory;
use backend\models\CountryStory;
use backend\models\CategoriesLevelOne;
use common\components\Helpers as CommonHelpers;

/**
 * StorySearch represents the model behind the search form about `backend\models\Story`.
 */
class StorySearch extends Story
{
    const SUBCATEGORY="subcategory";
    const CATEGORY="category";

    const LIMIT_STORY=10;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language_id', 'user_id'], 'integer'],
            [['title', 'seo_title', 'description', 'link', 'image', 'video', 'date_created', 'date_modified', 'status', 'date_published', 'seo_url'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchSponsored($params, $type, $categoryid, $date_published)
    {
        $tableCategoryStory=CategoryStory::tableName();
        $tableCountryStory=CountryStory::tableName();
        $tableStory=Story::tableName();

        //get language cookie
        $frontend_language_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);
        //get edition/country cookie
        $frontend_edition_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);
        $frontend_edition_cookie = explode("-", $frontend_edition_cookie); //explode to get array, 25-34-12

        //start query
        $query = Story::find();
        $query->joinWith(['relationUser', 'relationCountryStories'])
        ->with(['relationStoryClipkit', 'relationSponsoredStories'])
        ->where([$tableStory.'.status'=>Story::STATUS_PUBLISHED])
        ->andWhere(['sponsored_story' => 1])
        ->andWhere(['language_id'=>$frontend_language_cookie])
        ->andWhere(["IN", "$tableCountryStory.country_id", $frontend_edition_cookie])
        ->orderBy('RAND()')
        ;

        //load older stories after "LOAD MORE STORIES" is clicked
        if($date_published!=NULL)
        {
            $query->andWhere("date_published<'$date_published'"); // just "<" so you don't take that last story before "LOAD MORE STORIES"
        }



        //FILTERING BY CATEGORY
        //if level one category is set
        if($type == self::SUBCATEGORY)
        {
            $query->joinWith(['relationCategoryStories']);
            $query->andWhere(["$tableCategoryStory.category_id"=>$categoryid]);
            //$query->groupBy("$tableCategoryStory.story_id");  //because I'm making relation with category_stories where one story can be in more categories and multiple same stories are listed
        }
        //if parent category is set
        else if($type == self::CATEGORY)
        {
            $query->joinWith(['relationCategoryStories']);
            $query->andWhere(["IN","$tableCategoryStory.category_id", CategoriesLevelOne::getChildrenByParent($categoryid)]);
            //$query->groupBy("$tableCategoryStory.story_id");  //because I'm making relation with categori_stories where one story can be in more categories and multiple same stories are listed
        }

        return $query->all();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * $type=subcategory, category
     * $categoryid - id of specific subcategory /CategoriresLevelOne
     * $date_published if NULL that means that user didn't request new stories and you should take current time, if new stories are requested then take date_published < current_time else date_published <= current_time
     */
    public function searchStory($params, $type, $categoryid, $date_published)
    {
        $tableCategoryStory=CategoryStory::tableName();
        $tableCountryStory=CountryStory::tableName();
        $tableStory=Story::tableName();

        //get language cookie
        $frontend_language_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);
        //get edition/country cookie
        $frontend_edition_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_edition_country_id_cookie']);
        $frontend_edition_cookie = explode("-", $frontend_edition_cookie); //explode to get array, 25-34-12

        //start query
        $query = Story::find();
        $query->joinWith(['relationUser', 'relationCountryStories'])
        ->with(['relationStoryClipkit'])
        ->where([$tableStory.'.status'=>Story::STATUS_PUBLISHED])
        ->andWhere(['sponsored_story' => 0])
        //->andWhere([$tableStory.'.type' => [Story::TYPE_IMAGE, Story::TYPE_VIDEO]])
        ->andWhere(['language_id'=>$frontend_language_cookie])
        ->andWhere(["IN", "$tableCountryStory.country_id", $frontend_edition_cookie])
        ->andWhere(['<=', 'date_published', date('Y-m-d H:i:s')])
        ->orderBy('date_published DESC')
        ;

        //load older stories after "LOAD MORE STORIES" is clicked
        if($date_published!=NULL)
        {
            $query->andWhere("date_published<'$date_published'"); // just "<" so you don't take that last story before "LOAD MORE STORIES"
        }

        //FILTERING BY CATEGORY
        //if level one category is set
        if($type == self::SUBCATEGORY)
        {
            $query->joinWith(['relationCategoryStories']);
            $query->andWhere(["$tableCategoryStory.category_id"=>$categoryid]);
            //$query->groupBy("$tableCategoryStory.story_id");  //because I'm making relation with category_stories where one story can be in more categories and multiple same stories are listed
        }
        //if parent category is set
        else if($type == self::CATEGORY)
        {
            $query->joinWith(['relationCategoryStories']);
            $query->andWhere(["IN","$tableCategoryStory.category_id", CategoriesLevelOne::getChildrenByParent($categoryid)]);
            //$query->groupBy("$tableCategoryStory.story_id");  //because I'm making relation with categori_stories where one story can be in more categories and multiple same stories are listed
        }

        return $query->limit(self::LIMIT_STORY)->all();
        /*$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>
            [
                'pageSize'=>1,
            ],
            'totalCount'=>1000
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'id' => $this->id,
            'language_id' => $this->language_id,
            'date_created' => $this->date_created,
            'user_id' => $this->user_id,
            'date_modified' => $this->date_modified,
            'date_published' => $this->date_published,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;   */
    }
}
