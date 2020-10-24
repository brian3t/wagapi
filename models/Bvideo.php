<?php

namespace app\models;

use Yii;
use \app\models\base\Bvideo as BaseBvideo;

/**
 * This is the model class for table "bvideo".
 */
class Bvideo extends BaseBvideo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['band_id'], 'required'],
            [['band_id', 'processed_by'], 'integer'],
            [['created_at', 'updated_at', 'last_processed'], 'safe'],
            [['is_selected', 'seq'], 'string'],
            [['video_url'], 'string', 'max' => 800],
            [['note'], 'string', 'max' => 255]
        ]);
    }
	
}
