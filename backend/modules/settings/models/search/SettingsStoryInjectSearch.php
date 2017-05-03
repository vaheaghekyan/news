<?php

namespace backend\modules\settings\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\settings\models\SettingsStoryInject;

/**
 * SettingsStoryInjectSearch represents the model behind the search form about `backend\modules\settings\models\SettingsStoryInject`.
 */
class SettingsStoryInjectSearch extends SettingsStoryInject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language_id', 'country_id', 'frequency', 'type'], 'integer'],
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
        $query = SettingsStoryInject::find()->with(['relationLanguage', 'relationCountry']);

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
            'id' => $this->id,
            'language_id' => $this->language_id,
            'country_id' => $this->country_id,
            'frequency' => $this->frequency,
            'type' => $this->type,
        ]);

        return $dataProvider;
    }
}
