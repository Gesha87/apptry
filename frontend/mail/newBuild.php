<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $build \common\models\Build */

$appUrl = Yii::$app->urlManager->createAbsoluteUrl(['build/index', 'app_id' => $build->app_id]);
date_default_timezone_set('Asia/Dhaka');
$time = date('Y-m-d H:i:s');
?>
	<?= $time ?> (Barnaul)<br><br>

	New build of <?= Html::a($build->app->name, $appUrl) ?> is available!<br><br>

	Follow the link below to get new build:<br><br>

<?= Html::a(
	'Download',
	'itms-services://?action=download-manifest&url='.\yii\helpers\Url::to('/plists/'.$build->id.'/app.plist', 'https'),
	['class' => 'btn btn-success']
) ?>