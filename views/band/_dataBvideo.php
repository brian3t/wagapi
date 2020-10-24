<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

    $dataProvider = new ArrayDataProvider([
        'allModels' => $model->bvideos,
        'key' => 'id'
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute'=> 'video_url',
            'format' => 'raw',
            'value' => function ($m) {
                return \yii\helpers\Html::a(substr($m->video_url, 0, 80), $m->video_url, ['target' => '_blank']);
            }
        ],
        'is_selected',
        'seq',
        'note',
        'last_processed',
        [
                'attribute' => 'processedBy.username',
                'label' => 'Processed By'
            ],
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'bvideo'
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
