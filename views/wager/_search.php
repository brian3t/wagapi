<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WagerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-wager-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'type')->dropDownList([ 'winner' => 'Winner', 'sumpoint' => 'Sumpoint', 'freestyle' => 'Freestyle', 'pledge' => 'Pledge', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'init' => 'Init', 'pending' => 'Pending', 'accepted' => 'Accepted', 'calculated' => 'Calculated', 'withdrawn' => 'Withdrawn', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'pending_by')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\User::find()->orderBy('id')->asArray()->all(), 'id', 'username'),
        'options' => ['placeholder' => 'Choose User'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'accepted_by')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\User::find()->orderBy('id')->asArray()->all(), 'id', 'username'),
        'options' => ['placeholder' => 'Choose User'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php /* echo $form->field($model, 'game_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Game::find()->orderBy('id')->asArray()->all(), 'id', 'id'),
        'options' => ['placeholder' => 'Choose Game'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); */ ?>

    <?php /* echo $form->field($model, 'hwinner')->textInput(['placeholder' => 'Hwinner']) */ ?>

    <?php /* echo $form->field($model, 'win_margin')->textInput(['maxlength' => true, 'placeholder' => 'Win Margin']) */ ?>

    <?php /* echo $form->field($model, 'point')->textInput(['placeholder' => 'Point']) */ ?>

    <?php /* echo $form->field($model, 'pledge')->textInput(['maxlength' => true, 'placeholder' => 'Pledge']) */ ?>

    <?php /* echo $form->field($model, 'invited_at')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Invited At',
                'autoclose' => true,
            ]
        ],
    ]); */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
