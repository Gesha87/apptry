<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Crash;
use yii\data\Sort;
use yii\db\Expression;

/**
 * CrashSearch represents the model behind the search form about `common\models\Crash`.
 */
class CrashSearch extends Crash
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'app_id', 'build_id'], 'integer'],
            [['package_name', 'hash', 'hash_mini', 'stack_trace', 'stack_trace_mini', 'app_version', 'user_crash_date', 'device', 'system_version'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Crash::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'attributes' => [
					'id' => ['default' => SORT_DESC],
					'cnt' => ['default' => SORT_DESC],
					'user_crash_date' => ['default' => SORT_DESC]
				],
				'defaultOrder' => ['id' => SORT_DESC]
			],
			'pagination' => ['pageSize' => 10]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		$query->groupBy('hash_mini')->select([
			new Expression('max(id) id'),
			new Expression('count(id) cnt'),
			new Expression('max(app_version) app_version'),
			'stack_trace_mini',
			new Expression('max(user_crash_date) user_crash_date'),
			'hash_mini',
			new Expression('max(resolved) resolved'),
		]);

		if ($this->build_id) {
			$build = Build::findOne(['id' => $this->build_id]);
			if ($build) {
				$this->app_id = $build->app_id;
			}
		}

        $query->andFilterWhere([
            'app_id' => $this->app_id,
            'build_id' => $this->build_id,
        ]);

		if ($this->user_crash_date) {
			$parts = explode(' - ', $this->user_crash_date);
			if (count($parts) == 2) {
				$query->andWhere(['between', 'user_crash_date', $parts[0].' 00:00:00', $parts[1].' 23:59:59']);
			}
		}

        return $dataProvider;
    }

	public function graph($params)
	{
		$query = Crash::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => false,
			'pagination' => false
		]);
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->groupBy('crashed_date')->select([
			new Expression('COUNT(id) cnt'),
			new Expression('DATE_FORMAT(user_crash_date, \'%Y-%m-%d\') crashed_date'),
		]);

		if ($this->build_id) {
			$build = Build::findOne(['id' => $this->build_id]);
			if ($build) {
				$this->app_id = $build->app_id;
			}
		}

		$query->andFilterWhere([
			'app_id' => $this->app_id,
			'build_id' => $this->build_id,
		]);

		if (!$this->user_crash_date) {
			$to = strtotime(date('Y-m-d'));
			$from = $to - 3600 * 24 * 7;
			$this->user_crash_date = date('Y-m-d', $from) . ' - '. date('Y-m-d', $to);
		}
		$parts = explode(' - ', $this->user_crash_date);
		if (count($parts) == 2) {
			$query->andWhere(['between', 'user_crash_date', $parts[0].' 00:00:00', $parts[1].' 23:59:59']);
		}

		return $dataProvider;
	}
}
