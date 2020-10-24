<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * app\models\BandSearch represents the model behind the search form about `app\models\Band`.
 */
 class BandSearch extends Band
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['created_at', 'updated_at', 'name', 'logo', 'type', 'genre', 'similar_to', 'hometown_city', 'hometown_state', 'description', 'website', 'youtube', 'instagram', 'facebook', 'twitter', 'source', 'attr'], 'safe'],
            [['lno_score'], 'number'],
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
        $query = Band::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'lno_score' => $this->lno_score,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'genre', $this->genre])
            ->andFilterWhere(['like', 'similar_to', $this->similar_to])
            ->andFilterWhere(['like', 'hometown_city', $this->hometown_city])
            ->andFilterWhere(['like', 'hometown_state', $this->hometown_state])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'youtube', $this->youtube])
            ->andFilterWhere(['like', 'instagram', $this->instagram])
            ->andFilterWhere(['like', 'facebook', $this->facebook])
            ->andFilterWhere(['like', 'twitter', $this->twitter])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'attr', $this->attr]);

        return $dataProvider;
    }
}
