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
				return Html::a("<img class=app-icon src=$model->icon><span class=app-name>".$model->name.'</span><br><span class=app-bundle>'.$model->bundle_identifier.'<span>',
					\yii\helpers\Url::toRoute(['build/index', 'app_id' => $model->id]),
					['class' => 'app-link']);
			}],
			['attribute' => 'latest_build', 'format' => 'raw', 'value' => function($model) {
				$date = str_replace(' ', 'T', $model->last_update) . '+02:00';
				$time = strtotime($date) * 1000;
				return $model->build ? Html::a(
					$model->build->version,
					'itms-services://?action=download-manifest&url='.\yii\helpers\Url::to('/plists/'.$model->build->id.'/app.plist', 'https'),
					['class' => 'btn btn-success']
				)."<br><span class=\"timestamp\" data-timestamp=\"$time\"></span>" : null;
			}],
        ],
    ]); ?>

</div>
