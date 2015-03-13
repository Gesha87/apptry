<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "crash".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $build_id
 * @property string $package_name
 * @property string $hash
 * @property string $hash_mini
 * @property string $stack_trace
 * @property string $stack_trace_mini
 * @property string $app_version
 * @property string $user_crash_date
 * @property string $device
 * @property string $system_version
 * @property string $resolved
 */
class Crash extends \yii\db\ActiveRecord
{
	public $cnt;
	public $crashed_date;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'build_id'], 'integer'],
            [['package_name', 'hash', 'hash_mini', 'stack_trace', 'stack_trace_mini', 'app_version', 'device', 'system_version'], 'required'],
            [['user_crash_date'], 'safe'],
            [['package_name', 'hash', 'hash_mini', 'app_version', 'device', 'system_version'], 'string', 'max' => 255],
            [['stack_trace', 'stack_trace_mini'], 'string', 'max' => 6000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'app_id' => Yii::t('app', 'App ID'),
            'build_id' => Yii::t('app', 'Build ID'),
            'package_name' => Yii::t('app', 'Package Name'),
            'hash' => Yii::t('app', 'Hash'),
            'hash_mini' => Yii::t('app', 'Hash Mini'),
            'stack_trace' => Yii::t('app', 'Stack Trace'),
            'stack_trace_mini' => Yii::t('app', 'Stack Trace Mini'),
            'app_version' => Yii::t('app', 'App Version'),
            'user_crash_date' => Yii::t('app', 'User Crash Date'),
            'device' => Yii::t('app', 'Device'),
            'system_version' => Yii::t('app', 'System Version'),
        ];
    }
}
