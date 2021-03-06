<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $testers array */

$this->title = 'Testers';
$this->params['breadcrumbs'][] = $this->title;

$models = [];
foreach ($testers as $i => $attributes) {
	if ($i % 2 === 0) continue;
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
	'layout' => "<h4>Testers</h4>\n{items}",
	'columns' => [
		['attribute' => 'UDID', 'label' => 'UDID', 'format' => 'raw', 'value' => function($model) {
			$title = '';
			foreach ($model as $attr => $value) {
				$title .= "$attr: $value\n";
			}
			return Html::tag('span', $model['UDID'], [
				'class' => 'tester-udid',
				'data-toggle' => 'tooltip',
				'title' => $title,
			]);
		}],
		['class' => 'yii\grid\ActionColumn', 'template' => '{delete}', 'urlCreator' => function($action, $model) {
			return \yii\helpers\Url::toRoute(['site/delete-tester', 'id' => $model['UDID']]);
		}],
	],
]);

\yii\widgets\Pjax::end();