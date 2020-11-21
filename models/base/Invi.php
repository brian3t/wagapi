<?php

namespace app\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "invi".
 *
 * @property integer $id
 * @property integer $wager_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $invited_user_id
 * @property string $status
 *
 * @property \app\models\User $invitedUser
 * @property \app\models\Wager $wager
 */
class Invi extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'invitedUser',
            'wager'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wager_id', 'invited_user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invi';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wager_id' => 'Wager ID',
            'invited_user_id' => 'Invited User ID',
            'status' => 'Status',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitedUser()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'invited_user_id'])->inverseOf('invis');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWager()
    {
        return $this->hasOne(\app\models\Wager::className(), ['id' => 'wager_id'])->inverseOf('invis');
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
