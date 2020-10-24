<?php

namespace app\models;

use app\models\base\BandEvent as BaseBandEvent;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "band_event".
 */
class BandEvent extends BaseBandEvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['created_at', 'updated_at'], 'safe'],
            [['band_id', 'event_id'], 'required'],
            [['band_id', 'event_id', 'created_by'], 'integer']
        ]);
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), []);
    }

    public function extraFields()
    {
        return ['event','band'];
    }

}
