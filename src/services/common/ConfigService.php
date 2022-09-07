<?php

namespace davidxu\config\services\common;

use davidxu\base\enums\AppIdEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\common\Config;
use davidxu\config\models\common\ConfigValue;
use davidxu\config\services\Service;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class ConfigService
 * @package davidxu\config\services\common
 */
class ConfigService extends Service
{
    /**
     * Batch update
     *
     * @param string $app_id
     * @param array $data
     */
    public function updateAll(string $app_id, array $data)
    {
        $merchant_id = isset(Yii::$app->services->merchant) ? Yii::$app->services->merchant->getNotNullId() : 0;

        $query = Config::find();
        $model = new Config();
        $configs = $query->where(['in', 'name', array_keys($data)])
            ->andWhere(['app_id' => $app_id])
            ->with([
                'value' => function (ActiveQuery $subQuery) use ($merchant_id, $app_id, $model) {
                    $subQuery->andWhere(['app_id' => $app_id]);
                    if ($model->hasAttribute('merchant_id')) {
                        $subQuery->andFilterWhere(['merchant_id' => $merchant_id]);
                    }
                    return $subQuery;
                }
            ])
            ->all();

        /** @var Config $item */
//        $result = true;
//        $transaction = Yii::$app->getDb()->beginTransaction();
//        try {
            foreach ($configs as $item) {
                $val = $data[$item['name']] ?? '';
                /** @var ConfigValue $model */
                $model = $item->value ?? new ConfigValue();
                $model->merchant_id = $merchant_id;
                $model->config_id = $item->id;
                $model->app_id = $item->app_id;
                $model->data = is_array($val) ? Json::encode($val) : $val;
                $model->save();
            }

            if ($app_id === AppIdEnum::BACKEND) {
                Yii::$app->utility->backendConfigAll(true);
            } else {
                Yii::$app->utility->merchantConfigAll(true, $merchant_id);
            }
//            $transaction->commit();
//        } catch(\Exception $e) {
//            $transaction->rollBack();
//            return $result;
//        } catch(\Throwable $e) {
//            $transaction->rollBack();
//            return $result;
//        }
//        return $result;
    }

    /**
     * @param string $app_id App ID
     * @param int $merchant_id Merchant ID
     * @return array
     */
    public function findAllWithValue(string $app_id, int $merchant_id): array
    {
        $model = new Config();
        return Config::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->with([
                'value' => function (ActiveQuery $subQuery) use ($merchant_id, $app_id, $model) {
                    $subQuery->andWhere(['app_id' => $app_id]);
                    if ($model->hasAttribute('merchant_id')) {
                        $subQuery->andFilterWhere(['merchant_id' => $merchant_id]);
                    }
                    return $subQuery;
                }
            ])
            ->asArray()
            ->all();
    }
}
