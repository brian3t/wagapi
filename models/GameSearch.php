<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Game;

/**
 * app\models\GameSearch represents the model behind the search form about `app\models\Game`.
 */
 class GameSearch extends Game
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'teamh_id', 'teama_id', 'hpoint', 'apoint'], 'integer'],
            [['created_at', 'updated_at', 'game_date', 'game_time', 'location_text', 'note'], 'safe'],
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
        $query = Game::find();

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
            'teamh_id' => $this->teamh_id,
            'teama_id' => $this->teama_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'game_date' => $this->game_date,
            'game_time' => $this->game_time,
            'hpoint' => $this->hpoint,
            'apoint' => $this->apoint,
        ]);

        $query->andFilterWhere(['like', 'location_text', $this->location_text])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
