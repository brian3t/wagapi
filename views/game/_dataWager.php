<?php

use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

$dataProvider = new ArrayDataProvider([
        'allModels' => $model->wagers,
        'key' => 'id'
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'type',
        'status',
        [
                'attribute' => 'pendingBy.username',
                'label' => 'Pending By'
            ],
        [
                'attribute' => 'acceptedBy.username',
                'label' => 'Accepted By'
            ],
        'hwinner',
        'win_margin',
        'point',
        'pledge',
        'invited_at',
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'wager'
        ],
    ];

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'],
        'pjax' => true,
        'beforeHeader' => [
            [
                'options' => ['class' => 'skip-export']
            ]
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'showPageSummary' => false,
        'persistResize' => false,
    ]);
