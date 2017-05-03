<?php

namespace backend\modules\settings\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\settings\models\SettingsSocialNetworks;
use backend\models\Country;

/**
 * SettingsSocialNetworksSearch represents the model behind the search form about `backend\modules\settings\models\SettingsSocialNetworks`.
 */
class SettingsSocialNetworksSearch extends SettingsSocialNetworks
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'country_id'], 'integer'],
            [['social_network', 'group_concat_social_network_alias'], 'safe'],
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
        $tableSettingsSocialNetworks=SettingsSocialNetworks::getTableSchema();
        $tableCountry=Country::tableName();

        $query = SettingsSocialNetworks::find()
        ->select("GROUP_CONCAT(social_network SEPARATOR '<br>') AS group_concat_social_network_alias")
        ->addSelect("$tableSettingsSocialNetworks->name.*")
        ->joinWith(['relationCountry'])
        ->groupBy('country_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>
            [
                'defaultOrder'=>["relationCountry.name"=>SORT_ASC]
            ]
        ]);

        $dataProvider->sort->attributes['relationCountry.name'] = [
        'asc' => ["$tableCountry.name" => SORT_ASC],
        'desc' => ["$tableCountry.name" => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'country_id' => $this->country_id,
        ]);

        $query->andFilterWhere(['like', 'social_network', $this->group_concat_social_network_alias]);

        return $dataProvider;
    }
}
