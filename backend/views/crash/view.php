<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $hash string */
/* @var $packageName string */
/* @var $log string */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bug');
$this->params['breadcrumbs'][] = ['url' => 'javascript:history.go(-1)', 'label' => 'Bugs'];
$this->params['breadcrumbs'][] = $hash;

echo Html::tag('h2', Yii::t('app', 'Stack Trace'));
$lines = explode("\n", $log);
echo Html::beginTag('pre');
foreach ($lines as $line) {
	$line = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $line);
	if (preg_match('/\d+\s+'.$packageName.'+\s+(0x[0-9a-f]+)\s/', $line)) $line = '<span class="alert-success">'.$line.'</span>';
	if (preg_match('/Thread \d+ crashed:/i', $line)) $line = '<a name="crashed">'.$line.'</a>';
	echo $line.'<br>';
}
echo Html::endTag('pre');
echo Html::beginForm('', 'get', ['class' => 'well form-horizontal well-sm', 'onsubmit' => 'return false;']);
echo Html::checkbox('resolve', (bool)Yii::$app->redis->hget('apptry:resolved.bugs', $hash), [
	'label' => Yii::t('app', 'Resolve'),
	'class' => 'resolve',
	'data-hash' => $hash
]);
echo Html::endForm();

echo $this->render('_crashes', ['dataProvider' => $dataProvider]);