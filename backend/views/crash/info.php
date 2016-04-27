<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $crash \common\models\Crash */

$this->title = Yii::t('app', 'Crash');
$this->params['breadcrumbs'][] = 'Bugs';
$this->params['breadcrumbs'][] = ['url' => 'javascript:history.go(-1)', 'label' => 'Crashes'];
$this->params['breadcrumbs'][] = $crash->id;

echo Html::beginTag('pre');
echo Html::encode($crash->stack_trace);
echo Html::endTag('pre');