<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Game */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Game', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view">

    <div class="row">
        <div class="col-sm-8">
            <h2><?= 'Game'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-4" style="margin-top: 15px">
            <?= Html::a('Save As New', ['save-as-new', 'id' => $model->id], ['class' => 'btn btn-info']) ?>            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'teamh_id',
        'teama_id',
        'game_date',
        'game_time',
        'location_text',
        'note',
        'hpoint',
        'apoint',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerWager->totalCount){
    $gridColumnWager = [
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
    ];
    echo Gridview::widget([
        'dataProvider' => $providerWager,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-wager']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Wager'),
        ],
        'export' => false,
        'columns' => $gridColumnWager
    ]);
}
?>

    </div>
</div>
