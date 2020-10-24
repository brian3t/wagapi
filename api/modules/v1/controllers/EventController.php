<?php
/**
 * Event Controller REST
 */

namespace app\api\modules\v1\controllers;

use app\api\base\controllers\BaseActiveController;
use app\models\EventQuery;
use yii\data\ActiveDataProvider;

require_once realpath(dirname(dirname(dirname(dirname(__DIR__))))) . "/models/constants.php";
class EventController extends BaseActiveController
{
    // We are using the regular web app modules:
    public $modelClass = 'app\models\Event';

    public function actions()
    {
        $actions = parent::actions();
        // disable the default REST actions
//        unset($actions['index']);

        $actions['index']['prepareDataProvider'] = [$this, 'indexLastMonthPrepareDataProvider'];
        return $actions;
    }

    // prepare and return a data provider for the "index" action
    public function indexLastMonthPrepareDataProvider()
    {
        $params = \Yii::$app->getRequest()->getQueryParams();
        $date_start = $params['date_start'] ?? -3;
        $date_end = $params['date_end'] ?? 21;
//        $searchModel = new EventSearch();
//        $searchModel->search($params);
//        $dataProvider = $searchModel->search($params);
        unset($params['page']);
        $page_size = $params['page_size']??false;
        unset($params['page_size']);
        $query = new EventQuery($this->modelClass);
        $query->set_query_params($params);
        // get the total number of articles (but do not fetch the article data yet)
//        $count = $query->count();
        $dp = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => $page_size??20,
//                    'totalCount' => $count
                ],
            ]
        );
        $dp->setSort(['defaultOrder' => ['id' => SORT_DESC]]);
        if (YII_DEBUG) {
            $dp->pagination = false;
        }
//        return $query->createCommand()->rawSql;//zsdf
        return $dp;
    }

}
