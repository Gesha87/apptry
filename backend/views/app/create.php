<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\App */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'App',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
