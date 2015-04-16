<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $properties array */

date_default_timezone_set('Asia/Dhaka');
$time = date('Y-m-d H:i:s');
?>
	<?= $time ?> (Barnaul)<br><br>

	New tester came to us!<br><br>

<?= \yii\widgets\DetailView::widget([
	'model' => $properties
]); ?>