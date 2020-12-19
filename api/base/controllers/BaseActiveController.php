<?php

namespace app\api\base\controllers;

use app\api\base\RequestBody;
use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class BaseActiveController
 * @package app\api\base\controllers
 * @property RequestBody $requestbody
 * @property string $message
 */
class BaseActiveController extends ActiveController
{
//    public $serializer = 'tuyakhov\jsonapi\Serializer';

    /*public function behaviors() for jsonapi
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/vnd.api+json' => Response::FORMAT_JSON,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_JSON,
                ],
            ]
        ]);
    }*/

    public $requestbody;
    public $message;

    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        return ArrayHelper::merge([
            [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Methods' => ['GET', 'POST', 'OPTIONS', 'DELETE', 'PUT', 'PATCH'],
                ],
            ],
            // 'authenticator' => ['class' => HttpBasicAuth::className()]
        ], $behaviors);
    }

    public function actions()
    {
        $actions = parent::actions();
        return array_replace_recursive($actions, [
            'index' => [
                'class' => 'app\api\base\BaseIndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);
    }

    /** Error with an error message
     * @param $err_mess
     * @param int $status_code
     */
    protected function err($err_mess, $status_code = 500)
    {
        Yii::$app->getResponse()->statusCode = $status_code;
        return $err_mess;
    }

    public function beforeAction($action)
    {
        if (Yii::$app->request->method !== 'GET') return parent::beforeAction($action);
        $query_params = Yii::$app->request->getQueryParams();
        return parent::beforeAction($action);
    }
}
