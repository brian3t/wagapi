<?php

namespace app\models\base;

/**
 * This is the base model class for table "todo".
 *
 * @property integer $id
 * @property string $desc
 * @property integer $is_done
 * @property string $created_at
 * @property string $updated_at
 */
class Todo extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['desc'], 'required'],
            [['is_done'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['desc'], 'string', 'max' => 255],
            [['desc'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'todo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'desc' => 'Desc',
            'is_done' => 'Is Done',
        ];
    }
}
