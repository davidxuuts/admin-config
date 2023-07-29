<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services\common;

use davidxu\base\enums\AppIdEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\common\Config;
use davidxu\config\models\common\ConfigValue;
use davidxu\config\services\Service;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
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
     * @param array|null $data
     * @return bool
     */
    public function updateAll(string $app_id, array|null $data): bool
    {
        $merchant_id = isset(Yii::$app->services->merchantService)
            ? Yii::$app->services->merchantService->getId()
            : null;

        $query = Config::find()
            ->where(['in', 'name', array_keys($data)])
            ->andWhere(['app_id' => $app_id]);
        $model = new Config();
        if ($model->hasAttribute('merchant_id')) {
            $query->andWhere(['merchant_id' => $merchant_id]);
        }
        $configs = $query->all();
        /** @var Config $config */
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $configIds = $records = [];
            $fields = ['merchant_id', 'app_id', 'config_id', 'data'];
            foreach ($configs as $config) {
                $configIds[] = $config->id;
                $val = $data[$config->name] ?? null;
                $configData = is_array($val) ? Json::encode($val) : $val;
                $records[] = [$merchant_id, $config->app_id, $config->id, $configData];
            }
            ConfigValue::deleteAll(['config_id' => $configIds]);
            Yii::$app->db->createCommand()
                ->batchInsert(ConfigValue::tableName(), $fields, $records)
                ->execute();

            if ($app_id === AppIdEnum::BACKEND) {
                Yii::$app->utility->backendConfigAll(true);
            } else {
                Yii::$app->utility->merchantConfigAll(true, $merchant_id);
            }
            $transaction->commit();
            return true;
        } catch(Exception|Throwable) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @param string $app_id App ID
     * @param ?int $merchant_id Merchant ID
     * @return array
     */
    public function findAllWithValue(string $app_id, ?int $merchant_id = null): array
    {
        return Config::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->with([
                'value' => function (ActiveQuery $subQuery) use ($merchant_id, $app_id) {
                    $model = new Config();
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
