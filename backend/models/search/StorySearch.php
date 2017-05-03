<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use backend\models\Story;
use backend\models\Country;
use backend\models\Language;
use backend\models\CountryStory;
use backend\components\Helpers;
use backend\models\CategoryStory;
use backend\models\User;
/**
 * StorySearch represents the model behind the search form about `backend\models\Story`.
 */
class StorySearch extends Story
{
    public $filter_author;
    public $filter_media;
    public $filter_category;
    public $filter_country;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language_id', 'user_id'], 'integer'],
            [['title', 'seo_title', 'description', 'link', 'image', 'video', 'date_created', 'date_modified', 'status', 'date_published', 'filter_author', 'filter_media', 'filter_category', 'filter_country'], 'safe'],
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

    /*
    * default order of stories depending on status of story
    */
    private function defaultOrder()
    {
        $tableStory=Story::tableName();
        $action=Yii::$app->controller->action->id; //name of current action
        //if filter published stories
        if($action==strtolower(Story::STATUS_PUBLISHED))
            return ['date_published'=>SORT_DESC];
        //if you filter unpublished stories
        if($action==strtolower(Story::STATUS_UNPUBLISHED))
            return ['date_created'=>SORT_DESC];
        //if you filter any other stories
        else
            return [$tableStory.'.id' => SORT_DESC];

    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * $status (in Story.php):
            const STATUS_PENDING        = "PENDING APPROVAL";
            const STATUS_UNPUBLISHED    = "UNPUBLISHED";
            const STATUS_PUBLISHED      = "PUBLISHED";
            ...
     * $type - what type of story you want to show: "sponsored"
     */
    public function search($params, $status, $type=NULL)
    {
        $tableStory=Story::tableName();
        $tableCountry=Country::tableName();
        $tableUser=User::tableName();

        //find story only by this specific language
        $language       = new Language;
         
        $query = Story::find();
        $query->where([$tableStory.'.status'=>$status, "$tableStory.language_id" => $language->currentId]);
        $query->joinWith(['relationUser', 'relationCountryStories.relationCountry', 'relationCategoryStories.relationCategory.relationParentCategory']);


        //search only sponsored
        if($type=="sponsored")
            $query->andWhere(["sponsored_story"=>1]);
        else
            $query->andWhere(["sponsored_story"=>0]);

        //I had to put this here because totalCount always returned the sam value excluding filter query
        $query=$this->filters($query, $params);

        //if user used filter to filter news set totalCount differently
       // $totalCount=(!empty($params["StorySearch"])) ? $query->groupBy("$tableStory.id")->count() : 500;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['pageSize'=>100],
            'totalCount'=>$query->groupBy("$tableStory.id")->count() ,
            'sort'=>
            [
                'attributes'=>[$tableStory.'.id', 'link', 'filter_author', 'date_created', 'date_published'], //these are important if you want allow to sort it in gridView but also for defaulOrder to work if you want to order some extra fields
                'defaultOrder' => $this->defaultOrder()
            ],
        ]);

        $dataProvider->sort->attributes["filter_country"]=
        [
            'asc'=>["$tableCountry.name"=>SORT_ASC],
            'desc'=>["$tableCountry.name"=>SORT_DESC] ,
        ];

        $dataProvider->sort->attributes["filter_author"]=
        [
            'asc'=>["$tableUser.name"=>SORT_ASC],
            'desc'=>["$tableUser.name"=>SORT_DESC] ,
        ];

        return $dataProvider;

    }

    private function filters($query, $params)
    {
        $tableCountryStory=CountryStory::tableName();
        $tableUser=User::tableName();
        $tableCategoryStory=CategoryStory::tableName();
        $tableStories=Story::tableName();

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $query;
        }

        $query->andFilterWhere([
            $tableStories.'.id' => $this->id,
            'language_id' => $this->language_id,
            'user_id' => $this->user_id,
            $tableCountryStory.'.country_id' => $this->filter_country
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', $tableUser.'.name', $this->filter_author])
            ->andFilterWhere(['between', 'date_created', Helpers::createDateTimeBetween("begin", $this->date_created), Helpers::createDateTimeBetween("end", $this->date_created)])
            ->andFilterWhere(['between', 'date_published', Helpers::createDateTimeBetween("begin", $this->date_published), Helpers::createDateTimeBetween("end", $this->date_published)])
            ->andFilterWhere([$tableCategoryStory.'.category_id'=>$this->filter_category]);

        //filter image and video
        if(!empty($this->filter_media))
        {
            if($this->filter_media==Story::FILTER_IMAGE)
                $query->andWhere(['not', ['image' => null]]);

            else if($this->filter_media==Story::FILTER_VIDEO)
                $query->andWhere(['not', ['video' => null]]);

            else
                $query->andWhere(['not', ['video' => null]])->andWhere(['not', ['image' => null]]);
        }

        //advanced search to search between 2 dates
        if(isset($_GET["between_what"]) && !empty($_GET["between_start"]) && !empty($_GET["between_end"]))
        {
            $between_what=$_GET["between_what"];
            $between_start=Helpers::createDateTimeBetween("begin", $_GET["between_start"]);
            $between_end=Helpers::createDateTimeBetween("end", $_GET["between_end"]);

            switch($between_what)
            {
                case "date_published":
                    $query->andFilterWhere(['between', 'date_published', $between_start, $between_end]);
                    break;
                case "date_created":
                    $query->andFilterWhere(['between', 'date_created', $between_start, $between_end]);
                    break;
                case "date_modified":
                    $query->andFilterWhere(['between', 'date_modified', $between_start, $between_end]);
                    break;
            }
        }
        return $query;
    }
}