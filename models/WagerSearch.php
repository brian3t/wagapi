<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Wager;

/**
 * app\models\WagerSearch represents the model behind the search form about `app\models\Wager`.
 */
 class WagerSearch extends Wager
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'pending_by', 'accepted_by', 'game_id', 'hwinner', 'point'], 'integer'],
            [['created_at', 'updated_at', 'type', 'status', 'pledge', 'invited_at'], 'safe'],
            [['win_margin'], 'number'],
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
        $query = Wager::find();

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
            'pending_by' => $this->pending_by,
            'accepted_by' => $this->accepted_by,
            'game_id' => $this->game_id,
            'hwinner' => $this->hwinner,
            'win_margin' => $this->win_margin,
            'point' => $this->point,
            'invited_at' => $this->invited_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'pledge', $this->pledge]);

        return $dataProvider;
    }
}
