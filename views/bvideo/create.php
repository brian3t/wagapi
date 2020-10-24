<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Bvideo */

$this->title = 'Create Bvideo';
$this->params['breadcrumbs'][] = ['label' => 'Bvideo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bvideo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
