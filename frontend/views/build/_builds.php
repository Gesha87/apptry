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
	'layout' => "{pager}\n{items}\n{pager}",
	'pager' => [
		'firstPageLabel' => '&laquo;&laquo;',
		'lastPageLabel' => '&raquo;&raquo;',
	],
	'columns' => [
		['attribute' => 'version', 'label' => Yii::t('app', 'Build'), 'format' => 'html', 'value' => function($model) {
			return "<img class=app-icon src={$model->app->icon}><span class=build-version>".$model->version.'</span>';
		}],
		/*['attribute' => 'hash', 'format' => 'html', 'value' => function($model) {
			return $model->build ? Html::a(
				$model->build->version,
				'itms-services://?action=download-manifest&url='.\yii\helpers\Url::to('/plists/'.$model->build->id.'/app.plist', 'https'),
				['class' => 'btn btn-success']
			) : null;
		}],*/
		['class' => 'yii\grid\ActionColumn', 'template' => '{download}', 'buttons' => [
			'download' => function ($url, $model, $key) {
				return Html::a(
					'Download',
					'itms-services://?action=download-manifest&url='.\yii\helpers\Url::to('/plists/'.$model->id.'/app.plist', 'https'),
					['class' => 'btn btn-success']
				);
			}
		]]
	],
]);

\yii\widgets\Pjax::end();