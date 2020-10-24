<?php

namespace app\api\modules\v1\controllers;

use app\api\base\controllers\BaseActiveController;
use app\models\Band;
use yii\data\ActiveDataProvider;
use yii\rest\IndexAction;


require_once realpath(dirname(dirname(dirname(dirname(__DIR__))))). "/models/constants.php";
class BandController extends BaseActiveController
{
    // We are using the regular web app modules:
    public $modelClass = 'app\models\Band';

    public function actions()
    {
        $actions = parent::actions();

        // disable the default REST actions
//        unset($actions['index']);

        $actions['hasevent']['class']='app\api\modules\v1\controllers\BandRestHasevent';
        $actions['hasevent']['prepareDataProvider'] = [$this, 'haseventPrepareDataProvider'];
        return $actions;
    }

    // prepare and return a data provider for the "index" action
    public function haseventPrepareDataProvider()
    {
        $params = \Yii::$app->getRequest()->getQueryParams();
        unset($params['page']);
        $page_size = $params['page_size']??false;
        unset($params['page_size']);
        $event_date_start = $params['event_date_start'] ?? 0;
        $event_date_end = $params['event_date_end'] ?? 21;
//        $event_date_start = (new \DateTime())->sub(new \DateInterval("P${event_date_start}D"))->format('Y-m-d');
//        $event_date_end = (new \DateTime())->add(new \DateInterval("P${event_date_end}D"))->format('Y-m-d');
        $sql = '
        select *
FROM
  (select distinct band_id
   FROM (SELECT *
         FROM event
         WHERE date >= DATE_SUB(CURDATE(), INTERVAL :event_date_start DAY)
               AND date <= DATE_ADD(CURDATE(), INTERVAL :event_date_end DAY)) ev
     INNER JOIN (SELECT distinct band_id, event_id
                 FROM band_event) band_event on band_event.event_id = ev.id)
  band_performing
  INNER JOIN band b on band_performing.band_id = b.id';

        $dp = new ActiveDataProvider(
            [
                'query' => Band::findBySql($sql, [':event_date_start' => $event_date_start, ':event_date_end' => $event_date_end]),
//                'query' => \app\models\Event::find()->where(['>=', 'date_utc',$event_date_start])
//                    ->andWhere(['<=', 'date_utc', $event_date_end])->joinWith('bands'),
                'pagination' => [
                    'pageSize' => $page_size??20,
                ],
            ]
        );
        if (YII_DEBUG) {
            $dp->pagination = false;
        }
        return $dp;

    }



}

class BandRestHasevent extends IndexAction
{
    public $modelClass = 'app\models\Band';
}
