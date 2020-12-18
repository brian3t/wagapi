<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todo */

$this->title = 'Update Todo: ' . ' ' . $model->desc;
$this->params['breadcrumbs'][] = ['label' => 'Todo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->desc, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="todo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
