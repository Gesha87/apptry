<?php
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $testers array */

$models = [];
foreach ($testers as $udid => $attributes) {
	$models[] = \yii\helpers\Json::decode($attributes);
}
$dataProvider = new \yii\data\ArrayDataProvider([
	'key' => 'UDID',
	'allModels' => $models,
	'sort' => false,
	'pagination' => false,
]);

\yii\widgets\Pjax::begin([
	'enablePushState' => false,
	'enableReplaceState' => true,
]);

echo GridView::widget([
	'id' => 'testers-grid-view',
	'dataProvider' => $dataProvider,
	'columns' => [
		['attribute' => 'UDID', 'label' => 'UDID'],
		['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
	],
]);

\yii\widgets\Pjax::end();