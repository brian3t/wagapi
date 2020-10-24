<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * app\models\EventSearch represents the model behind the search form about `app\models\Event`.
 */
 class EventSearch extends Event
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'user_id', 'venue_id'], 'integer'],
            [['created_at', 'updated_at', 'date', 'start_time', 'end_time', 'when', 'name', 'short_desc', 'description', 'img', 'cost', 'is_charity', 'age_limit', 'twitter', 'facebook', 'website', 'system_note', 'sdr_name', 'temp', 'source', 'attr'], 'safe'],
            [['min_cost', 'max_cost'], 'number'],
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
        $query = Event::find();

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
            'created_by' => $this->created_by,
            'user_id' => $this->user_id,
            'venue_id' => $this->venue_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'min_cost' => $this->min_cost,
            'max_cost' => $this->max_cost,
        ]);

        $query->andFilterWhere(['like', 'when', $this->when])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_desc', $this->short_desc])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'cost', $this->cost])
            ->andFilterWhere(['like', 'is_charity', $this->is_charity])
            ->andFilterWhere(['like', 'age_limit', $this->age_limit])
            ->andFilterWhere(['like', 'twitter', $this->twitter])
            ->andFilterWhere(['like', 'facebook', $this->facebook])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'system_note', $this->system_note])
            ->andFilterWhere(['like', 'sdr_name', $this->sdr_name])
            ->andFilterWhere(['like', 'temp', $this->temp])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'attr', $this->attr]);

        return $dataProvider;
    }
}
