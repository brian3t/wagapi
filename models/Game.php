<?php

namespace app\models;

use Yii;
use \app\models\base\Game as BaseGame;

/**
 * This is the model class for table "game".
 */
class Game extends BaseGame
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['teamh_id', 'teama_id', 'game_date'], 'required'],
            [['teamh_id', 'teama_id', 'hpoint', 'apoint'], 'integer'],
            [['created_at', 'updated_at', 'game_date', 'game_time'], 'safe'],
            [['location_text'], 'string', 'max' => 800],
            [['note'], 'string', 'max' => 2000]
        ]);
    }
	
}
