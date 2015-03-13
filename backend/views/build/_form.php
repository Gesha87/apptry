<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Build */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="build-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'added_date')->textInput() ?>

    <?= $form->field($model, 'icon')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'hash')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
