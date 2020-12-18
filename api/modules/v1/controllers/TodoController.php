<?php

namespace app\api\modules\v1\controllers;

use app\api\base\controllers\BaseActiveController;


require_once realpath(dirname(dirname(dirname(dirname(__DIR__))))). "/models/constants.php";
class TodoController extends BaseActiveController
{
    // We are using the regular web app modules:
    public $modelClass = 'app\models\Todo';

    public function actions()
    {
        $actions = parent::actions();

        // disable the default REST actions
//        unset($actions['index']);

        return $actions;
    }

    // prepare and return a data provider for the "index" action


}
