<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \common\models\CrashSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

Pjax::begin([
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
		['attribute' => 'id', 'label' => '#', 'format' => 'text', 'value' => function($model) {
			return $model->id;
		}],
		['attribute' => 'cnt', 'label' => Yii::t('app', 'Count'), 'format' => 'raw', 'value' => function($model) {
			$class = $model['cnt'] > Yii::$app->params['countDanger'] ? 'alert-danger' : '';
			return "<span class=\"badge $class\">{$model->cnt}</span>";
		}],
		['attribute' => 'stack_trace_mini', 'label' => Yii::t('app', 'Info'), 'format' => 'raw', 'value' => function($model) {
			return Html::a(Html::tag('pre', $model->stack_trace_mini, [
				'class' => 'stack-trace-mini',
				//'data-toggle' => 'tooltip',
				//'title' => $model->stack_trace_mini,
			]), Url::toRoute(['crash/view', 'hash' => $model->hash_mini]), ['data-pjax' => 0]);
		}],
		['attribute' => 'app_version', 'label' => Yii::t('app', 'Version'), 'format' => 'text', 'value' => function($model) {
			return str_replace(' ', '', $model->app_version);
		}],
		['attribute' => 'user_crash_date', 'label' => Yii::t('app', 'Crashed'), 'format' => 'raw', 'value' => function($model) {
			return '<span class="glyphicon glyphicon-time"></span>&nbsp;'.$model->user_crash_date;
		}],
		['attribute' => 'res', 'label' => Yii::t('app', 'Resolved'), 'format' => 'raw', 'value' => function($model) {
			return Html::checkbox('resolve', (bool)Yii::$app->redis->hget('apptry:resolved.bugs', $model->hash_mini), [
				'class' => 'resolve',
				'data-hash' => $model->hash_mini
			]);
		}],
	],
	'rowOptions' => function ($model, $key, $index, $grid) {
		$resolved = Yii::$app->redis->hget('apptry:resolved.bugs', $model->hash_mini);
		return ['class' => $resolved ? ($resolved >= $model->app_version ? 'alert-success' : 'alert-danger') : ''];
	}
]);
Pjax::end();