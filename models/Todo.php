<?php

namespace app\models;

use app\models\base\Todo as BaseTodo;

/**
 * This is the model class for table "todo".
 */
class Todo extends BaseTodo implements \tuyakhov\jsonapi\ResourceInterface
{
    use \tuyakhov\jsonapi\ResourceTrait;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['desc'], 'required'],
            [['is_done'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['desc'], 'string', 'max' => 255],
            [['desc'], 'unique']
        ]);
    }

}
