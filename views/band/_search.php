<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BandSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-band-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']) ?>

    <?= $form->field($model, 'source')->widget(\kartik\widgets\Select2::classname(), [
        'data' => ['sdr'=>'San Diego Reader (sdr)','ticketfly'=>'TicketFly','reverb'=>'Reverb','tickmas'=>'Ticket Master (tickmas)','other'=>'Other','unknown'=>'Unknown'],
        'options' => ['placeholder' => 'Choose Source'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\User::find()->orderBy('id')->asArray()->all(), 'id', 'username'),
        'options' => ['placeholder' => 'Choose User'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'logo')->textInput(['maxlength' => true, 'placeholder' => 'Logo']) ?>

    <?= $form->field($model, 'lno_score')->textInput(['maxlength' => true, 'placeholder' => 'Lno Score']) ?>

    <?php /* echo $form->field($model, 'type')->dropDownList([ 'covers' => 'Covers', 'originals' => 'Originals', 'covers & originals' => 'Covers & originals', 'unknown' => 'Unknown', ], ['prompt' => '']) */ ?>

    <?php /* echo $form->field($model, 'genre')->textInput(['maxlength' => true, 'placeholder' => 'Genre']) */ ?>

    <?php /* echo $form->field($model, 'similar_to')->textInput(['maxlength' => true, 'placeholder' => 'Similar To']) */ ?>

    <?php /* echo $form->field($model, 'hometown_city')->textInput(['maxlength' => true, 'placeholder' => 'Hometown City']) */ ?>

    <?php /* echo $form->field($model, 'hometown_state')->textInput(['maxlength' => true, 'placeholder' => 'Hometown State']) */ ?>

    <?php /* echo $form->field($model, 'description')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'website')->textInput(['maxlength' => true, 'placeholder' => 'Website']) */ ?>

    <?php /* echo $form->field($model, 'youtube')->textInput(['maxlength' => true, 'placeholder' => 'Youtube']) */ ?>

    <?php /* echo $form->field($model, 'instagram')->textInput(['maxlength' => true, 'placeholder' => 'Instagram']) */ ?>

    <?php /* echo $form->field($model, 'facebook')->textInput(['maxlength' => true, 'placeholder' => 'Facebook']) */ ?>

    <?php /* echo $form->field($model, 'twitter')->textInput(['maxlength' => true, 'placeholder' => 'Twitter']) */ ?>

    <?php /* echo $form->field($model, 'source')->dropDownList([ 'sdr' => 'Sdr', 'ticketfly' => 'Ticketfly', 'other' => 'Other', 'unknown' => 'Unknown', 'reverb' => 'Reverb', ], ['prompt' => '']) */ ?>

    <?php /* echo $form->field($model, 'attr')->textInput(['placeholder' => 'Attr']) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
