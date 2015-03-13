<?php

namespace frontend\controllers;

use Yii;
use common\models\App;
use common\models\Build;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BuildController extends Controller
{
	public function actionIndex()
	{
		$appId = Yii::$app->request->getQueryParam('app_id');
		if (!$appId || !($app = App::findOne(['id' => $appId]))) {
			throw new NotFoundHttpException('Application not found!');
		}

		$dataProvider = new ActiveDataProvider([
			'query' => Build::find()->where(['app_id' => $appId])->orderBy('id DESC'),
			'sort' => false,
			'pagination' => [
				'pageSize' => 8,
			],
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'app' => $app,
		]);
	}
}
