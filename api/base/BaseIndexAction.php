<?php

namespace app\api\base;

use yii\data\ActiveDataProvider;
use yii\rest\IndexAction;

class BaseIndexAction extends IndexAction
{
    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        $modelClass = new $this->modelClass();
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }

        /* @var $modelClass \yii\db\BaseActiveRecord */
        $params = \Yii::$app->request->queryParams;
        unset($params['expand']);
        unset($params['page']);
        $page_size = $params['page_size']??false;
        unset($params['page_size']);
        $ap = new ActiveDataProvider([
            'query' => $modelClass::find()->where($params),
        ]);
        if($page_size) {
            $ap->pagination->setPageSize($page_size);
        }
        return $ap;
    }

}
