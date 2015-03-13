<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

\yii\widgets\Pjax::begin([
	'enablePushState' => false,
	'enableReplaceState' => true,
]);

echo GridView::widget([
	'id' => 'builds-grid-view',
	'dataProvider' => $dataProvider,
	'layout' => "{summary}\n{pager}\n{items}\n{pager}",
	'pager' => [
		'firstPageLabel' => '&laquo;&laquo;',
		'lastPageLabel' => '&raquo;&raquo;',
	],
	'columns' => [
		['attribute' => 'version', 'label' => Yii::t('app', 'Build'), 'format' => 'html', 'value' => function($model) {
			return "<img class=app-icon src={$model->app->icon}><span class=build-version>".$model->version.'</span>';
		}],
		['attribute' => 'added_date', 'format' => 'html', 'value' => function($model) {
			return Html::tag('span', '', ['class' => 'glyphicon glyphicon-time']).'&nbsp;'.$model->added_date;
		}],
		['attribute' => 'count_crashes', 'label' => Yii::t('app', 'Crashes'), 'format' => 'raw', 'value' => function($model) {
			return Html::a($model->count_crashes, \yii\helpers\Url::toRoute(['crash/index', 'CrashSearch[build_id]' => $model->id]), ['data-pjax' => 0]);
		}],
	],
]);

\yii\widgets\Pjax::end();