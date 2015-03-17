<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Apps');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
		'id' => 'apps-grid-view',
		'layout' => "{items}",
        'dataProvider' => $dataProvider,
        'columns' => [
			['attribute' => 'name', 'label' => Yii::t('app', 'App'), 'format' => 'html', 'value' => function($model) {
				return "<img class=app-icon src=$model->icon><span class=app-name>".$model->name.'</span><br><span class=app-bundle>'.$model->bundle_identifier.'<span>';
			}],
			['attribute' => 'last_update', 'format' => 'html', 'value' => function($model) {
				return Html::tag('span', '', ['class' => 'glyphicon glyphicon-time']).'&nbsp;'.$model->last_update;
			}],
			['attribute' => 'latest_build', 'format' => 'html', 'value' => function($model) {
				return $model->build ? Html::a($model->build->version, \yii\helpers\Url::toRoute(['build/index', 'app_id' => $model->id])) : null;
			}],
			['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
        ],
    ]); ?>

</div>
