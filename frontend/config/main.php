<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'apptry-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
	'defaultRoute' => 'app/index',
    'components' => [
        'user' => [
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
		],
		'request' => [
			'enableCsrfValidation' => false,
		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
				[
					'class' => 'common\log\AccessFileTarget',
					'logFile' => '@runtime/logs/access.log',
					'levels' => ['info'],
					'categories' => ['access'],
					'logVars' => [],
				],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
					'logVars' => ['_POST'],
                ],
            ],
        ],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'<controller:\w+>' => '<controller>/index',
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => 'smtp.sendgrid.com',
				'username' => 'TagBrand',
				'password' => 'sendGridTagBrand7',
				'port' => '587',
				'encryption' => 'tls',
			],
		]
    ],
    'params' => $params,
];
