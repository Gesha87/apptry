<?php

namespace backend\controllers;

use Yii;
use common\models\App;
use common\models\Build;
use common\models\Crash;
use common\models\CrashSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CrashController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$searchModel = new CrashSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (Yii::$app->request->getIsPjax()) {
			return $this->render('_bugs', ['dataProvider' => $dataProvider, 'model' => $searchModel]);
		} else {
			$apps = ArrayHelper::map(App::find()->all(), 'id', 'name');
			$dataGraph = [];
			$graphModel = new CrashSearch();
			$graphProvider = $graphModel->graph(Yii::$app->request->queryParams);
			$to = strtotime(date('Y-m-d'));
			$from = $to - 3600 * 24 * 7;
			if ($graphModel->user_crash_date) {
				$parts = explode(' - ', $graphModel->user_crash_date);
				if (count($parts) == 2) {
					$from = strtotime($parts[0]);
					$to = strtotime($parts[1]);
				}
			}
			for ($i = $from; $i <= $to; $i += 3600 * 24) {
				$dataGraph[$i] = array($i * 1000, 0);
			}

			foreach ($graphProvider->getModels() as $point) {
				$date = strtotime($point->crashed_date);
				$dataGraph[$date] = array($date * 1000, (int)$point->cnt);
			}
			ksort($dataGraph);
			$series[] = array(
				'data' => array_values($dataGraph),
				'type' => 'spline',
				'shadow' => true,
				'marker' => array(
					'enabled' => true,
					'radius' => 3
				)
			);

			return $this->render('index', [
				'model' => $searchModel,
				'dataProvider' => $dataProvider,
				'apps' => $apps,
				'series' => $series
			]);
		}
	}

	public function actionView()
	{
		$hash = Yii::$app->request->getQueryParam('hash');
		$dataProvider = new ActiveDataProvider([
			'query' => Crash::find()
				->where(['hash_mini' => $hash])
				->orderBy('id DESC'),
			'sort' => false,
			'pagination' => ['pageSize' => 10],
		]);
		if (!$dataProvider->getCount()) {
			throw new NotFoundHttpException('Bug not found!');
		}

		if (Yii::$app->request->getIsPjax()) {
			return $this->render('_crashes', [
				'dataProvider' => $dataProvider,
			]);
		} else {
			$models = $dataProvider->getModels();
			$crash = reset($models);
			$log = $crash->stack_trace;
			$log = substr($log, 0, strpos($log, 'Binary Images:'));
			$packageName = $crash->package_name;

			return $this->render('view', [
				'dataProvider' => $dataProvider,
				'packageName' => $packageName,
				'log' => $log,
				'hash' => $hash
			]);
		}
	}

	public function actionResolve()
	{
		$redis = Yii::$app->redis;
		$hash = Yii::$app->request->post('hash');
		$version = (int)Yii::$app->request->post('version');

		if ($version) {
			$version = Crash::find()->where(['hash_mini' => $hash])->select('MAX(app_version)')->scalar();
		} else {
			$version = null;
		}
		Crash::updateAll(['resolved' => $version], ['hash_mini' => $hash]);
		if ($version) {
			$redis->hset('apptry:resolved.bugs', $hash, $version);
		} else {
			$redis->hdel('apptry:resolved.bugs', $hash);
		}
	}

	public function actionInfo()
	{
		$id = Yii::$app->request->getQueryParam('id');
		$crash = Crash::findOne(['id' => $id]);
		if (!$crash) {
			throw new NotFoundHttpException('Crash not found!');
		}

		return $this->render('info', ['crash' => $crash]);
	}
}
