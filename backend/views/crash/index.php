<?php
use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $apps array */
/* @var $model \common\models\CrashSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

cakebake\bootstrap\select\BootstrapSelectAsset::register($this, [
	'selector' => '.selectpicker',
	'menuArrow' => true,
	'tickIcon' => false,
	'selectpickerOptions' => [
		'style' => 'btn-info form-control',
		'noneSelectedText' => '',
	],
]);
$this->title = Yii::t('app', 'Bugs');

$form = ActiveForm::begin([
	'id' => 'form-filter',
	'method' => 'get',
	'action' => '/crash?',
	'layout' => 'inline',
	'fieldConfig' => [
		'template' => "{beginWrapper}\n{input}\n{endWrapper}",
	],
]);
	echo $form->field($model, 'app_id')->dropDownList($apps, [
		'class' => 'selectpicker',
		'onchange' => '$("#crash-build-filter").val(""); $("#form-filter").submit()',
	]);
	echo '&nbsp;';
	echo Html::beginTag('div', ['class' => 'form-group field-crashsearch-build-id']);
		echo $this->render('_builds', ['model' => $model]);
	echo Html::endTag('div');
	echo '&nbsp;';
	echo $form->field($model, 'user_crash_date')->widget(DateRangePicker::className(), [
		'readonly' => true,
		'presetDropdown' => true,
		'convertFormat' => true,
		'callback' => 'function() { $("#form-filter").submit(); }',
		'options' => [
			'id' => 'crash-daterange',
			'placeholder' => Yii::t('app', 'Select period'),
		],
		'pluginOptions' => [
			'separator' => ' - ',
			'format' => 'Y-m-d',
			'maxDate' => date('Y-m-d'),
		],
	]);
	echo '&nbsp;';
	echo Html::beginTag('div', ['class' => 'form-group']);
		echo Html::button(Yii::t('app', 'Clear'), [
			'class' => 'btn btn-warning',
			'onclick' => '$("#crash-daterange").val(""); $("#form-filter").submit();',
		]);
		echo Html::submitButton(Yii::t('app', 'Submit'), [
			'class' => 'hidden',
		]);
	echo Html::endTag('div');
$form->end();
echo \miloschuman\highcharts\Highstock::widget([
	'id' => 'chart',
	'options' => [
		'navigator' => ['enabled' => false],
		'scrollbar' => ['enabled' => false],
		'navigation' => ['buttonOptions' => []],
		'title' => ['text' => Yii::t('app', 'Stability of application')],
		'rangeSelector' => false,
		'colors' => ['#DB343D'],
		'xAxis' => [
			'dateTimeLabelFormats' => ['hour' => ' ']
		],
		'yAxis' => [
			'opposite' => false,
			'title' => ['text' => Yii::t('app', 'Crashes')]
		],
		'series' => $series,
		'plotOptions' => [
			'spline' => [
				'animation' => false,
				'dataLabels' => ['enabled' => true],
				'dataGrouping'=> ['approximation'=> 'sum'],
				'enableMouseTracking'=> false,
				'shadow'=> true
			],
		],
	]
]);

echo $this->render('_bugs', ['dataProvider' => $dataProvider, 'model' => $model]);

