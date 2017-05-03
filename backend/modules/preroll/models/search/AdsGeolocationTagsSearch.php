<?php

namespace backend\modules\preroll\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\preroll\models\AdsGeolocationTags;

/**
 * AdsGeolocationTagsSearch represents the model behind the search form about `backend\modules\preroll\models\AdsGeolocationTags`.
 */
class AdsGeolocationTagsSearch extends AdsGeolocationTags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagId'], 'integer'],
            [['tagName', 'tagUrl'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdsGeolocationTags::find();
        $query->orderBy("tagId DESC");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tagId' => $this->tagId,
        ]);

        $query->andFilterWhere(['like', 'tagName', $this->tagName])
            ->andFilterWhere(['like', 'tagUrl', $this->tagUrl]);

        return $dataProvider;
    }
}
