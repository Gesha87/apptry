<?php
namespace frontend\controllers;

use CFPropertyList\CFPropertyList;
use Exception;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	public function actionAbout()
	{
		return $this->render('about');
	}

	public function actionUdid()
	{
		$data = file_get_contents('php://input');
		$xml = substr($data, strpos($data, '<plist '));
		$xml = substr(substr($data, strpos($data, '<plist ')), 0, strpos($xml, '</plist>') + 8);
		$this->redirect(['site/result', 'data' => $xml], 301);
	}

	public  function actionResult()
	{
		$properties = [];
		$plist = new CFPropertyList();
		try {
			$plist->parse($_GET['data']);
			$properties = $plist->toArray();
		} catch (Exception $ex) {}

		$this->render('result', ['properties' => $properties]);
	}
}
