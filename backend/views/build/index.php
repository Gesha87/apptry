<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $app \common\models\App */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Builds');
$this->params['breadcrumbs'][] = $app->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_builds', ['dataProvider' => $dataProvider]); ?>

</div>
