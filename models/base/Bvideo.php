<?php

namespace app\models\base;

use Yii;

/**
 * This is the base model class for table "bvideo".
 *
 * @property integer $id
 * @property integer $band_id
 * @property string $video_url
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_selected
 * @property integer $seq
 * @property string $note
 * @property string $last_processed
 * @property integer $processed_by
 *
 * @property \app\models\User $processedBy
 * @property \app\models\Band $band
 */
class Bvideo extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'processedBy',
            'band'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['band_id'], 'required'],
            [['band_id', 'processed_by'], 'integer'],
            [['created_at', 'updated_at', 'last_processed'], 'safe'],
            [['is_selected', 'seq'], 'string'],
            [['video_url'], 'string', 'max' => 800],
            [['note'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bvideo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'band_id' => 'Band ID',
            'video_url' => 'Video Url',
            'is_selected' => 'Is Selected',
            'seq' => 'Seq',
            'note' => 'Note',
            'last_processed' => 'Last Processed',
            'processed_by' => 'Processed By',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['id' => 'processed_by'])->inverseOf('bvideos');
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBand()
    {
        return $this->hasOne(\app\models\Band::className(), ['id' => 'band_id'])->inverseOf('bvideos');
    }
    }
