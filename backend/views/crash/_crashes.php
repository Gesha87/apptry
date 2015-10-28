<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

\yii\widgets\Pjax::begin([
	'enablePushState' => false,
	'enableReplaceState' => true,
]);
echo \yii\grid\GridView::widget([
	'dataProvider' => $dataProvider,
	'layout' => "{pager}\n{items}\n{pager}",
	'tableOptions' => [
		'class' => 'table table-bordered table-condensed',
	],
	'pager' => [
		'firstPageLabel' => '&laquo;&laquo;',
		'lastPageLabel' => '&raquo;&raquo;',
	],
	'columns' => [
		['attribute' => 'app_version', 'label' => Yii::t('app', 'Build'), 'format' => 'text', 'value' => function($model) {
			return str_replace(' ', '', $model->app_version);
		}],
		['attribute' => 'system_version', 'label' => Yii::t('app', 'OS Version'), 'format' => 'text'],
		['attribute' => 'device', 'label' => Yii::t('app', 'Device'), 'format' => 'text'],
		['attribute' => 'user_crash_date', 'label' => Yii::t('app', 'Crashed'), 'format' => 'raw', 'value' => function($model) {
			return Html::tag('span', '', ['class' => 'glyphicon glyphicon-time']).'&nbsp;'.$model->user_crash_date;
		}],
		['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'urlCreator' => function($action, $model) {
			return \yii\helpers\Url::toRoute(['crash/info', 'id' => $model->id]);
		}]
	],
	'rowOptions' => function ($model, $key, $index, $grid) {
		return ['class' => $model->resolved ? 'alert-success' : ''];
	}
]);
\yii\widgets\Pjax::end();