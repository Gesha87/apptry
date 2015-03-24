<?php
/* @var $this yii\web\View */
/* @var $properties array */

$this->title = 'Result';
$this->params['breadcrumbs'][] = $this->title;

\yii\widgets\DetailView::widget([
	'model' => $properties
]);

