<?php
use common\models\Build;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
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
		['attribute' => 'comment', 'label' => Yii::t('app', 'Comment'), 'format' => 'raw', 'value' => function($model) {
			return nl2br($model->comment) . ' ' . Html::a(
				'<i class="glyphicon glyphicon-pencil"></i>',
				'#',
				['title' => 'Edit', 'class' => 'btn btn-link btn-xs build-edit', 'data' => [
					'id' => $model->id,
					'comment' => $model->comment,
					'toggle' => 'tooltip',
					'pjax' => 0,
				]]
			);
		}],
		['attribute' => 'count_crashes', 'label' => Yii::t('app', 'Crashes'), 'format' => 'raw', 'value' => function($model) {
			return Html::a($model->count_crashes, \yii\helpers\Url::toRoute(['crash/index', 'CrashSearch[build_id]' => $model->id]), ['data-pjax' => 0]);
		}],
		['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
	],
]);

$this->registerJs(<<<JS
	$('.build-edit').click(function() {
		$('#build-edit-modal').modal('show');
		$('#build-id').val($(this).data('id'));
		$('#build-comment').val($(this).data('comment'));
		return false;
	});
JS
);

\yii\widgets\Pjax::end();

Modal::begin([
	'header' => '<h2>Edit build</h2>',
	'toggleButton' => false,
	'id' => 'build-edit-modal'
]);
	$model = new Build();
	$form = ActiveForm::begin(['action' => ['build/save-comment']]) ?>

		<input id="build-id" name="buildId" type="hidden" value="">

		<?= $form->field($model, 'comment')->textarea(['id' => 'build-comment']) ?>

		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end();
Modal::end();