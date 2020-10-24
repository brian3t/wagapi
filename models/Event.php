<?php

namespace app\models;

use app\models\base\Event as BaseEvent;
use usv\yii2helper\models\ModelB3tTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event".
 */
class Event extends BaseEvent
{
    use ModelB3tTrait;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBands()
    {
        return $this->hasMany(\app\models\Band::className(), ['id' => 'band_id'])->via('bandEvents')->inverseOf('events');
    }

    public function fields()
    {
//        return parent::fields();
        return ArrayHelper::merge(parent::fields(), ['band_events' => 'bandEvents', 'venue']);
    }

    public function beforeSave($insert)
    {
        if ($this->start_datetime_utc != null && $this->start_time == null) {
            $this->start_time_utc = (new \DateTime($this->start_datetime_utc))->format('H:i:s');
        }
        if ($this->start_datetime_utc != null && $this->date_utc == null) {
            $this->date_utc = (new \DateTime($this->start_datetime_utc))->format('Y-m-d');
        }
        return parent::beforeSave($insert);
    }

    public function getFirst_band(){
        $band_events = $this->bandEvents;
        if (sizeof($band_events) < 1) return [];
        $first_band_event = $band_events[0];
        if (!$first_band_event instanceof BandEvent) return [];
        $first_band = $first_band_event->getBand();
        return $first_band;
    }

    public function extraFields()
    {
        return ['bands','first_band'];
    }
}
