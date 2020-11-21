<?php

namespace app\models;

use app\models\base\Game as BaseGame;
use app\models\Wager as Wager;
use yii\db\Expression as Expression;

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

    /**
     * After game is updated
     * If winner is determined, let's go and assign points to wagers
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (! array_key_exists('hpoint', $changedAttributes) && ! array_key_exists('apoint', $changedAttributes))
            return;//if we didn't change hpoint, didn't change apoint, just exit
        if (! is_numeric($this->hpoint) || ! is_numeric($this->apoint))
            return;//if we're missing hpoint or apoint, we can't determine winner yet

        //determine who the winner was
        $winner = ($this->hpoint > $this->apoint) ? 'home' : 'away';
        $un_calculated_wags = Wager::find()->where(['calculated_at' => null])->all();
        foreach ($un_calculated_wags as $un_calculated_wag) {
            /** @var Wager $un_calculated_wag */
            $wag_creator = $un_calculated_wag->createdBy;
            $point_to_add = 0;
            if ($un_calculated_wag->hwinner) {//predicted home
                if ($winner === 'home') $point_to_add = $un_calculated_wag->point;
                else $point_to_add = -1 * $un_calculated_wag->point;
            } else {//predicted away
                if ($winner === 'away') $point_to_add = $un_calculated_wag->point;
                else $point_to_add = -1 * $un_calculated_wag->point;
            }
            $wag_creator->point += $point_to_add;
            $wag_creator->save();

            $wag_accepted_person = $un_calculated_wag->acceptedBy;
            if (is_object($wag_accepted_person)){
                $wag_accepted_person->point += $point_to_add;
                $wag_accepted_person->save();
            }
            $un_calculated_wag->status = 'calculated';
            $un_calculated_wag->calculated_at = new Expression('NOW()');
            $un_calculated_wag->save();

            $a = 1;
        }
        $a = 1;

    }
}
