<?php

namespace app\models;

use Yii;
use \app\models\base\Invi as BaseInvi;

/**
 * This is the model class for table "invi".
 */
class Invi extends BaseInvi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['wager_id', 'invited_user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string']
        ]);
    }
	
}
