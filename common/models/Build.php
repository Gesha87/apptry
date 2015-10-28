<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "build".
 *
 * @property integer $id
 * @property integer $app_id
 * @property string $added_date
 * @property string $hash
 * @property string $version
 * @property string $plist
 * @property integer $count_crashes
 * @property string $comment
 */
class Build extends \yii\db\ActiveRecord
{
	public $visible_version;
	public $inner_version;

	public function init()
	{
		parent::init();
		$this->on(self::EVENT_BEFORE_INSERT, [$this, 'initAttributes']);
	}

	public function initAttributes()
	{
		if ($this->inner_version && $this->visible_version) {
			$version = $this->visible_version.' ('.$this->inner_version.')';
			$count = Build::find()->where('version LIKE :version AND app_id = :app')->addParams([':version' => $version.'%', ':app' => $this->app_id])->count();
			$this->version = $this->visible_version.' ('.$this->inner_version.')'.($count ? (' #' . ($count + 1)) : '');
		}
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'build';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['added_date'], 'safe'],
			[['app_id'], 'integer'],
            [['visible_version', 'inner_version', 'hash'], 'required'],
            [['plist'], 'string', 'max' => 512],
			[['comment'], 'string', 'max' => 1024],
            [['hash', 'version'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'added_date' => Yii::t('app', 'Added Date'),
            'name' => Yii::t('app', 'Name'),
            'hash' => Yii::t('app', 'Hash'),
        ];
    }

	public function getApp()
	{
		return $this->hasOne(App::className(), ['id' => 'app_id']);
	}
}
