<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GameSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-game-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'teamh_id')->textInput(['placeholder' => 'Teamh']) ?>

    <?= $form->field($model, 'teama_id')->textInput(['placeholder' => 'Teama']) ?>

    <?= $form->field($model, 'game_date')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Game Date',
                'autoclose' => true
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'game_time')->widget(\kartik\datecontrol\DateControl::className(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_TIME,
        'saveFormat' => 'php:H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Game Time',
                'autoclose' => true
            ]
        ]
    ]); ?>

    <?php /* echo $form->field($model, 'location_text')->textInput(['maxlength' => true, 'placeholder' => 'Location Text']) */ ?>

    <?php /* echo $form->field($model, 'note')->textInput(['maxlength' => true, 'placeholder' => 'Note']) */ ?>

    <?php /* echo $form->field($model, 'hpoint')->textInput(['placeholder' => 'Hpoint']) */ ?>

    <?php /* echo $form->field($model, 'apoint')->textInput(['placeholder' => 'Apoint']) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
