<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Wager */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wager', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wager-view">

    <div class="row">
        <div class="col-sm-8">
            <h2><?= 'Wager'.' '. Html::encode($this->title) ?></h2>
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

    <div class="row">
<?php
if($providerInvi->totalCount){
    $gridColumnInvi = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        [
                'attribute' => 'invitedUser.username',
                'label' => 'Invited User'
            ],
            'status',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerInvi,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-invi']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Invi'),
        ],
        'export' => false,
        'columns' => $gridColumnInvi
    ]);
}
?>

    </div>
    <div class="row">
        <h4>Accepted by User: <?= ' '. Html::encode($model->acceptedBy? $model->acceptedBy->username: 'N/A') ?></h4>
    </div>
    <?php
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
        'email',
        'password_hash',
        'auth_key',
        'unconfirmed_email',
        'registration_ip',
        'flags',
        'confirmed_at',
        'blocked_at',
        'last_login_at',
        'last_login_ip',
        'auth_tf_key',
        'auth_tf_enabled',
        'password_changed_at',
        'gdpr_consent',
        'gdpr_consent_date',
        'gdpr_deleted',
        'point',
    ];
    if (is_object($model->acceptedBy)) {
        echo DetailView::widget([
            'model' => $model->acceptedBy,
            'attributes' => $gridColumnUser]);
    }
    ?>
    <div class="row">
        <h4>Created by User: <?= ' '. Html::encode($model->createdBy?$model->createdBy->username:'N/A') ?></h4>
    </div>
    <?php
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
        'email',
        'point',
    ];
    if (is_object($model->createdBy)) {
        echo DetailView::widget([
            'model' => $model->createdBy,
            'attributes' => $gridColumnUser]);
    }
    ?>
    <div class="row">
        <h4>Game<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php
    $gridColumnGame = [
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
        'model' => $model->game,
        'attributes' => $gridColumnGame    ]);
    ?>
    <div class="row">
        <h4>Pending User to accept: <?= ' ' . Html::encode($model->pendingBy ? $model->pendingBy->username : 'N/A') ?></h4>
    </div>
    <?php
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
        'email',
        'password_hash',
        'auth_key',
        'unconfirmed_email',
        'registration_ip',
        'flags',
        'confirmed_at',
        'blocked_at',
        'last_login_at',
        'last_login_ip',
        'auth_tf_key',
        'auth_tf_enabled',
        'password_changed_at',
        'gdpr_consent',
        'gdpr_consent_date',
        'gdpr_deleted',
        'point',
    ];
    if (is_object($model->pendingBy)) {
        echo DetailView::widget([
            'model' => $model->pendingBy,
            'attributes' => $gridColumnUser]);
    }
    ?>
</div>
