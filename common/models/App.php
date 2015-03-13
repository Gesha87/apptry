<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "app".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property string $bundle_identifier
 * @property string $last_update
 * @property integer $latest_build
 */
class App extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'app';
    }

    public function rules()
    {
        return [
            [['name', 'bundle_identifier'], 'required'],
            [['last_update'], 'safe'],
            [['latest_build'], 'integer'],
            [['name', 'bundle_identifier'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 512]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '#'),
            'name' => Yii::t('app', 'Name'),
            'icon' => Yii::t('app', 'Icon'),
            'bundle_identifier' => Yii::t('app', 'Bundle Identifier'),
            'last_update' => Yii::t('app', 'Last Updated'),
            'latest_build' => Yii::t('app', 'Latest Build'),
        ];
    }

	public function getBuild()
	{
		return $this->hasOne(Build::className(), ['id' => 'latest_build']);
	}
}
