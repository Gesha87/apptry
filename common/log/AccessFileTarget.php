<?php
namespace common\log;

use Yii;
use yii\log\FileTarget;
use yii\log\Logger;

class AccessFileTarget extends FileTarget
{
	public function formatMessage($message)
	{
		list(,,,$timestamp) = $message;
		$userId = Yii::$app->has('user') ? Yii::$app->user->id : 0;
		$url = Yii::$app->request->getUrl();
		$referrer = Yii::$app->request->getReferrer();
		$ip = Yii::$app->request->getUserIP();
		$userAgent = Yii::$app->request->getUserAgent();

		return date('Y-m-d H:i:s', $timestamp) . " [$userId] $ip '$url' '$referrer' '$userAgent'";
	}
}
