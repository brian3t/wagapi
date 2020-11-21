<?php

namespace app\models;

use app\models\base\Wager as BaseWager;

/**
 * This is the model class for table "wager".
 */
class Wager extends BaseWager
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['created_at', 'updated_at', 'invited_at', 'calculated_at'], 'safe'],
            [['type', 'status'], 'string'],
            [['created_by', 'game_id'], 'required'],
            [['created_by', 'pending_by', 'accepted_by', 'game_id', 'hwinner', 'point'], 'integer'],
            [['win_margin'], 'number'],
            [['pledge'], 'string', 'max' => 800]
        ]);
    }

}
