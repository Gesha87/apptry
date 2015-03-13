<?php
use common\models\Build;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\CrashSearch */

$builds = ArrayHelper::map(Build::find()->where(['app_id'=>$model->app_id])->all(), 'id', 'version');
echo Html::activeDropDownList($model, 'build_id', $builds, [
	'id' => 'crash-build-filter',
	'class' => 'selectpicker',
	'prompt' => Yii::t('app', 'All builds'),
	'onchange' => '$("#form-filter").submit()',
]);