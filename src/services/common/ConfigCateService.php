<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services\common;

use davidxu\base\enums\StatusEnum;
use davidxu\config\services\Service;
use yii\db\ActiveQuery;
use davidxu\config\models\common\ConfigCate;
use davidxu\config\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class ConfigCateService
 * @package davidxu\config\services\common
 */
class ConfigCateService extends Service
{
    /**
     * @param int|string $app_id
     * @return array
     */
    public function getDropDown(int|string $app_id): array
    {
        $models = ArrayHelper::itemsMerge($this->findAll($app_id));

        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');
    }

    /**
     * Get dropdown list for categories
     *
     * @param int|string $app_id
     * @param int|null $id
     * @return array
     */
    public function getDropDownForEdit(int|string $app_id, ?int $id = null)
    {
        $list = ConfigCate::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['app_id' => $app_id])
            ->andFilterWhere(['<>', 'id', $id])
            ->select(['id', 'title', 'pid', 'level'])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()
            ->all();

        $models = ArrayHelper::itemsMerge($list);
        $data = ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');

        return ArrayHelper::merge([null => Yii::t('configtr', 'Top category')], $data);
    }

    /**
     * 获取关联配置信息的递归数组
     *
     * @param int|string $app_id
     * @return array
     */
    public function getItemsMergeForConfig(int|string $app_id): array
    {
        return ArrayHelper::itemsMerge($this->findAllWithConfig($app_id));
    }

    /**
     * @param int|string $app_id
     * @param int|string $cate_id
     * @return array
     */
    public function getChildIds(int|string $app_id, int|string $cate_id): array
    {
        $categories = $this->findAll($app_id);
        $cateIds = ArrayHelper::getChildIds($categories, $cate_id);
        $cateIds[] = $cate_id;

        return $cateIds;
    }

    /**
     * 关联配置的列表
     *
     * @param int|string $app_id
     * @return array|ActiveRecord[]
     */
    public function findAllWithConfig(int|string $app_id): array
    {
        return ConfigCate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->orderBy(['order' => SORT_ASC])
            ->with([
                'config' => function (ActiveQuery $query) use ($app_id) {
                    return $query->andWhere(['app_id' => $app_id])
                        ->with([
                            'value' => function (ActiveQuery $query) {
                                return $query->andWhere(['merchant_id' => $this->getMerchantId()]);
                            }
                        ]);
                }
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param int|string $app_id
     * @return array|ActiveRecord[]
     */
    public function findAll(int|string $app_id): array
    {
        return ConfigCate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->asArray()
            ->all();
    }
}
