<?php
namespace frontend\controllers;

use Yii;
use common\models\App;
use common\models\Build;
use common\models\Crash;
use frontend\components\ErrorHandler;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * Api controller
 */
class ApiController extends Controller
{
	public $modelNameForModelIdentifier = [
		'iPhone1,1' => 'iPhone 1G',
		'iPhone1,2' => 'iPhone 3G',
		'iPhone2,1' => 'iPhone 3GS',
		'iPhone3,1' => 'iPhone 4 (GSM)',
		'iPhone3,2' => 'iPhone 4 (GSM Rev A)',
		'iPhone3,3' => 'iPhone 4 (CDMA)',
		'iPhone4,1' => 'iPhone 4S',
		'iPhone5,1' => 'iPhone 5 (GSM)',
		'iPhone5,2' => 'iPhone 5 (Global)',
		'iPhone5,3' => 'iPhone 5c (GSM)',
		'iPhone5,4' => 'iPhone 5c (Global)',
		'iPhone6,1' => 'iPhone 5s (GSM)',
		'iPhone6,2' => 'iPhone 5s (Global)',
		'iPhone7,1' => 'iPhone 6 Plus',
		'iPhone7,2' => 'iPhone 6',

		'iPad1,1' => 'iPad 1G',
		'iPad2,1' => 'iPad 2 (Wi-Fi)',
		'iPad2,2' => 'iPad 2 (GSM)',
		'iPad2,3' => 'iPad 2 (CDMA)',
		'iPad2,4' => 'iPad 2 (Rev A)',
		'iPad3,1' => 'iPad 3 (Wi-Fi)',
		'iPad3,2' => 'iPad 3 (GSM)',
		'iPad3,3' => 'iPad 3 (Global)',
		'iPad3,4' => 'iPad 4 (Wi-Fi)',
		'iPad3,5' => 'iPad 4 (GSM)',
		'iPad3,6' => 'iPad 4 (Global)',

		'iPad4,1' => 'iPad Air (Wi-Fi)',
		'iPad4,2' => 'iPad Air (Cellular)',
		'iPad5,3' => 'iPad Air 2 (Wi-Fi)',
		'iPad5,4' => 'iPad Air 2 (Cellular)',

		'iPad2,5' => 'iPad mini 1G (Wi-Fi)',
		'iPad2,6' => 'iPad mini 1G (GSM)',
		'iPad2,7' => 'iPad mini 1G (Global)',
		'iPad4,4' => 'iPad mini 2G (Wi-Fi)',
		'iPad4,5' => 'iPad mini 2G (Cellular)',
		'iPad4,7' => 'iPad mini 3G (Wi-Fi)',
		'iPad4,8' => 'iPad mini 3G (Cellular)',
		'iPad4,9' => 'iPad mini 3G (Cellular)',

		'iPod1,1' => 'iPod touch 1G',
		'iPod2,1' => 'iPod touch 2G',
		'iPod3,1' => 'iPod touch 3G',
		'iPod4,1' => 'iPod touch 4G',
		'iPod5,1' => 'iPod touch 5G',
	];

	public function init()
	{
		parent::init();
		$errorHandler = new ErrorHandler();
		$errorHandler->register();
		Yii::$app->response->format = Response::FORMAT_JSON;
		Yii::$app->response->data = [
			'data' => null,
			'error' => [
				'code' => 0,
				'message' => '',
			],
		];
	}

	public function actionLoad()
	{
		$dwarfdump = Yii::$app->request->post('dwarfdump');
		$ipa = Yii::$app->request->post('ipa');
		$hash = Yii::$app->request->post('hash');
		if ($image = UploadedFile::getInstanceByName('image')) {
			$ext = $image->getExtension();
			$name = Yii::$app->request->post('name');
			if (!$image->saveAs(Yii::getAlias('@webroot').'/img/'.$name.'.'.$ext)) {
				Yii::error('Couldn\'t save icon');
			} else {
				$_POST['icon'] = Yii::$app->request->hostInfo.'/img/'.$name.'.'.$ext;
			}
		}
		$count = preg_match_all('/UUID: ([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\s*/i', $dwarfdump, $matches);
		if (!$count) {
			throw new BadRequestHttpException("Couldn't get uuids");
		}
		if (!$ipa) {
			throw new BadRequestHttpException("ipa link can not be blank");
		}
		$transaction = Yii::$app->db->beginTransaction();
		$app = App::findOne(['bundle_identifier' => Yii::$app->request->post('bundle_identifier')]);
		$build = new Build();
		if (!$app) {
			$app = new App();
		} else {
			$build->app_id = $app->id;
		}
		$attributes['Build'] = $_POST;
		if ($build->load($attributes) && $build->save()) {
			//
		} else {
			$transaction->rollBack();
			$message = 'Bad Data';
			if ($build->hasErrors()) {
				$errors = $build->getFirstErrors();
				$message = reset($errors);
			}
			throw new BadRequestHttpException($message);
		}
		$app->latest_build = $build->id;
		$attributes['App'] = $_POST;
		if ($app->load($attributes) && $app->save()) {
			//
		} else {
			$transaction->rollBack();
			$message = 'Bad Data';
			if ($app->hasErrors()) {
				$errors = $app->getFirstErrors();
				$message = reset($errors);
			}
			throw new BadRequestHttpException($message);
		}
		$build->app_id = $app->id;
		$build->update();
		$transaction->commit();
		$uuids = [];
		foreach ($matches[1] as $uuid) {
			$uuids[] = strtolower(strtr($uuid, ['-' => '']));
		}
		foreach ($uuids as $uuid) {
			Yii::$app->redis->hset('uuid.to.hash', $uuid, $hash);
		}
		$plist = <<<PLIST
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>items</key>
    <array>
        <dict>
            <key>assets</key>
            <array>
                <dict>
                    <key>kind</key>
                    <string>software-package</string>
                    <key>url</key>
                    <string>$ipa</string>
                </dict>
                <dict>
                    <key>kind</key>
                    <string>display-image</string>
                    <key>needs-shine</key>
                    <false/>
                    <key>url</key>
                    <string>$app->icon</string>
                </dict>
            </array>
            <key>metadata</key>
            <dict>
                <key>bundle-identifier</key>
                <string>$app->bundle_identifier</string>
                <key>bundle-version</key>
                <string>$build->inner_version</string>
                <key>kind</key>
                <string>software</string>
                <key>title</key>
                <string>$app->name</string>
            </dict>
        </dict>
    </array>
</dict>
</plist>
PLIST;
		$dir = Yii::getAlias('@webroot').'/plists/'.$build->id;
		mkdir($dir);
		file_put_contents($dir.'/app.plist', $plist);
		Yii::$app->mailer->compose('newBuild', ['build' => $build])
			->setFrom('build@apptry.com')
			->setTo(Yii::$app->params['emails'])
			->setSubject('New Build')
			->send();
		Yii::$app->response->data['data'] = ['status' => true];
	}

	public function actionSendCrash()
	{
		$xmlstring = Yii::$app->request->post('xmlstring');
		if ($xmlstring) {
			$crashes = simplexml_load_string($xmlstring);
			foreach ($crashes->crash as $crash) {
				$appName = (string)$crash->applicationname;
				$log = (string)$crash->log;
				$appVersion = (string)$crash->version;
				$parts = explode('.', $appVersion);
				$parts = array_map(function($elem) { return str_pad($elem, 3, ' ', STR_PAD_LEFT); }, $parts);
				$appVersion = implode('.', $parts);
				$model = (string)$crash->platform;
				if (isset($this->modelNameForModelIdentifier[$model])) {
					$model = $this->modelNameForModelIdentifier[$model] . ' (' . $model . ')';
				}
				$systemVersion = (string)$crash->systemversion;
				$miniLog = ' Empty';
				preg_match('/(0x[0-9a-f]+)\s+-\s+0x[0-9a-f]+\s+\+?'.$appName.'\s+(.+)\s+<([0-9a-f]+)>/', $log, $matches);
				$appId = $buildId = null;
				if ($matches) {
					$architecture = $matches[2];
					$uuid = $matches[3];
					$hash = Yii::$app->redis->hget('uuid.to.hash', $uuid);
					$build = Build::find()->with('app')->where(['hash' => $hash])->one();
					if ($build) {
						$buildId = $build->id;
						$appId = $build->app_id;
					}
					$binaryImageAddresses = [];
					$countBinaryImages = preg_match_all('/(0x[0-9a-f]+)\s+-\s+0x[0-9a-f]+\s+\+?(\S+)\s+(.+?)\s+/', $log, $binaryImagesMatches);
					if ($countBinaryImages) {
						foreach ($binaryImagesMatches[1] as $i => $imageLoadAddress) {
							$binaryImageAddresses[$binaryImagesMatches[2][$i]] = $imageLoadAddress;
						}
					}
					preg_match('/Thread \d+ Crashed:(.*?)Thread \d+/is', $log, $crashedMatches);
					$crashed = $crashedMatches ? $crashedMatches[1] : '';
					$count = preg_match_all('/\n\d+\s+(\S+)+\s+(0x[0-9a-f]+)\s+.+/', $crashed, $addressMatches);
					$symbolicate = [];
					if ($count) {
						$linesMini = [];
						foreach($addressMatches[1] as $i => $binaryImage) {
							if (isset($binaryImageAddresses[$binaryImage])) {
								$symbolicate[$binaryImageAddresses[$binaryImage]][] = $addressMatches[2][$i];
							}
							if ($binaryImage == $appName) {
								$linesMini[] = $addressMatches[0][$i];
							}
						}
						$linesMini = array_map(function($v) { return trim($v); }, $linesMini);
						$linesMini and $miniLog = implode("\n", $linesMini);
						if ($hash) {
							foreach ($symbolicate as $loadAddress => $addresses) {
								$output = $this->symbolicate($hash, $architecture, $loadAddress, implode(' ', $addresses), @$build->app->product_name);
								if ($output && is_array($output)) {
									foreach ($output as $i => $line) {
										$address = @$addresses[$i];
										if ($address && strcmp($address, $line)) {
											$log = preg_replace('/(\n\d+\s+\S+\s+'.$address.'\s+).+/', '$1' . $line, $log);
											$miniLog = preg_replace('/(\d+\s+\S+\s+'.$address.'\s+).+/', '$1' . $line, $miniLog, 1);
										}
									}
								}
							}
						} else {
							Yii::error("Could not find conformity uuid ($uuid) to hash");
						}
						$miniLog = preg_replace(['/\d+\s+\S+\s+0x[0-9a-f]+\s+/', '/[\t\p{Zs}]+/'], ['', ' '], $miniLog);
					}
				} else {
					Yii::error("Could not find $appName in Binary Images");
					return;
				}
				$userCrashDate = date('Y-m-d H:i:s');
				preg_match('/Date\/Time:\s+(.*)/', $log, $matches);
				if ($matches) {
					$userCrashDate = date('Y-m-d H:i:s', strtotime($matches[1]));
				}

				$attributes = [
					'app_id' => $appId,
					'build_id' => $buildId,
					'package_name' => $appName,
					'hash' => md5($log),
					'hash_mini' => md5($miniLog),
					'stack_trace' => $log,
					'stack_trace_mini' => $miniLog,
					'app_version' => $appVersion,
					'user_crash_date' => $userCrashDate,
					'device' => $model,
					'system_version' => $systemVersion
				];
				$crashModel = new Crash();
				$crashModel->setAttributes($attributes);
				$crashModel->save(false);

				if ($buildId) {
					Build::updateAllCounters(['count_crashes' => 1], ['id' => $buildId]);
				}

				Yii::$app->response->data['data'] = [
					'status' => true
				];
			}
		} else {
			Yii::$app->response->data['error'] = [
				'code' => 1,
				'message' => 'Missing "xmlstring" param!',
			];
		}
	}

	protected function symbolicate($hash, $architecture, $loadAddress, $addresses, $productName)
	{
		$headers = array(
			'Content-Type: application/x-www-form-urlencoded'
		);
		$fields = array(
			'hash' => $hash,
			'load_address' => $loadAddress,
			'addresses' => $addresses,
			'architecture' => $architecture,
			'product_name' => $productName
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Yii::$app->params['atosUrl']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		$result = curl_exec($ch);
		if ($result === false) {
			throw new NotFoundHttpException('Atos error: '.curl_error($ch));
		}
		curl_close($ch);

		return json_decode($result);
	}
}
