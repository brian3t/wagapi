<?php

namespace app\models;

use app\models\base\Band as BaseBand;
use usv\yii2helper\models\ModelB3tTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "band".
 */
class Band extends BaseBand
{
    use ModelB3tTrait;

    public function beforeValidate()
    {
        try {
            $genre_array = \Yii::$app->request->getBodyParam('genre_array');
        } catch (\Exception $e) {
            $genre_array = null;
        }
        if (is_array($genre_array)) {
            $this->genre = implode(',', $genre_array);
        }
        if (! is_string($this->attr)) $this->attr = "";
//        if (is_array($this->attr)) $this->attr=json_encode($this->attr);
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if (isset($this['ytlink_first'])) {
            $this['ytlink_first'] = str_replace('https://www.youtube.com/watch?v=', '', $this['ytlink_first']);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\app\models\Event::className(), ['id' => 'event_id'])->via('bandEvents')->inverseOf('bands');
    }

    public function pull_events()
    {
        $query = $this->hasMany(\app\models\Event::className(), ['id' => 'event_id'])->viaTable('band_event', ['band_id' => 'id'])
//            ->joinWith('profile')
//            ->addSelect(['*', "profile.name AS name"]);
        ;
        return $query;
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), ['band_events' => 'bandEvents']);
    }

    public function extraFields()
    {
        return [['band_events' => 'bandEvents'], 'events'];
    }

}
