<?php

use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\helpers\Url;

$items = [
    [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode('Mp'),
        'content' => $this->render('_detail', [
            'model' => $model,
        ]),
    ],
        [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode('Order'),
        'content' => $this->render('_dataOrder', [
            'model' => $model,
            'row' => $model->orders,
        ]),
    ],
    ];
echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
        //        'height' => TabsX::SIZE_TINY
    ],
    'pluginEvents' => [
        "tabsX.click" => "function(e) {setTimeout(function(e){
                if ($('.nav-tabs > .active').next('li').length == 0) {
                    $('#prev').show();
                    $('#next').hide();
                } else if($('.nav-tabs > .active').prev('li').length == 0){
                    $('#next').show();
                    $('#prev').hide();
                }else{
                    $('#next').show();
                    $('#prev').show();
                };
                console.log(JSON.stringify($('.active', '.nav-tabs').html()));
            },10)}",
    ],
]);
?>
