<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "game".
 *
 * @property integer $id
 * @property integer $teamh_id
 * @property integer $teama_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $game_date
 * @property string $game_time
 * @property string $location_text
 * @property string $note
 * @property integer $hpoint
 * @property integer $apoint
 *
 * @property \app\models\Wager[] $wagers
 */
class Game extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'wagers'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamh_id', 'teama_id', 'game_date'], 'required'],
            [['teamh_id', 'teama_id', 'hpoint', 'apoint'], 'integer'],
            [['created_at', 'updated_at', 'game_date', 'game_time'], 'safe'],
            [['location_text'], 'string', 'max' => 800],
            [['note'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamh_id' => 'Teamh ID',
            'teama_id' => 'Teama ID',
            'game_date' => 'Game Date',
            'game_time' => 'Game Time',
            'location_text' => 'Location Text',
            'note' => 'Note',
            'hpoint' => 'Hpoint',
            'apoint' => 'Apoint',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagers()
    {
        return $this->hasMany(\app\models\Wager::className(), ['game_id' => 'id'])->inverseOf('game');
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
            'uuid' => [
                'class' => UUIDBehavior::className(),
                'column' => 'id',
            ],
        ];
    }
}
