<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Wager */

?>
<div class="wager-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($model->id) ?></h2>
        </div>
    </div>

    <div class="row">
<?php
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'type',
        'status',
        [
            'attribute' => 'pendingBy.username',
            'label' => 'Pending By',
        ],
        [
            'attribute' => 'acceptedBy.username',
            'label' => 'Accepted By',
        ],
        [
            'attribute' => 'game.id',
            'label' => 'Game',
        ],
        'hwinner',
        'win_margin',
        'point',
        'pledge',
        'invited_at',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
</div>
