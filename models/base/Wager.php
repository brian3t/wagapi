<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "wager".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $type
 * @property string $status
 * @property integer $created_by
 * @property integer $pending_by
 * @property integer $accepted_by
 * @property integer $game_id
 * @property integer $hwinner
 * @property string $win_margin
 * @property integer $point
 * @property string $pledge
 * @property string $invited_at
 *
 * @property \app\models\Invi[] $invis
 * @property \app\models\User $acceptedBy
 * @property \app\models\User $createdBy
 * @property \app\models\Game $game
 * @property \app\models\User $pendingBy
 */
class Wager extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'invis',
            'acceptedBy',
            'createdBy',
            'game',
            'pendingBy'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'invited_at'], 'safe'],
            [['type', 'status'], 'string'],
            [['created_by', 'game_id'], 'required'],
            [['created_by', 'pending_by', 'accepted_by', 'game_id', 'hwinner', 'point'], 'integer'],
            [['win_margin'], 'number'],
            [['pledge'], 'string', 'max' => 800],
            [['lock'], 'default', 'value' => '0'],
            [['lock'], 'mootensai\components\OptimisticLockValidator']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wager';
    }

    /**
     *
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock
     *
     */
    public function optimisticLock() {
        return 'lock';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'status' => 'Status',
            'pending_by' => 'Pending By',
            'accepted_by' => 'Accepted By',
            'game_id' => 'Game ID',
            'hwinner' => 'Hwinner',
            'win_margin' => 'Win Margin',
            'point' => 'Point',
            'pledge' => 'Pledge',
            'invited_at' => 'Invited At',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvis()
    {
        return $this->hasMany(\app\models\Invi::className(), ['wager_id' => 'id'])->inverseOf('wager');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcceptedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'accepted_by'])->inverseOf('wagers');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'created_by'])->inverseOf('wagers');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(\app\models\Game::className(), ['id' => 'game_id'])->inverseOf('wagers');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendingBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'pending_by'])->inverseOf('wagers');
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
                'updatedByAttribute' => 'updated_by',
            ],
            'uuid' => [
                'class' => UUIDBehavior::className(),
                'column' => 'id',
            ],
        ];
    }
}
