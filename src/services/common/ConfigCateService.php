<?php

namespace davidxu\config\services\common;

use davidxu\base\enums\StatusEnum;
use davidxu\config\services\Service;
use yii\db\ActiveQuery;
use davidxu\config\models\common\ConfigCate;
use davidxu\config\helpers\ArrayHelper;
use Yii;

/**
 * Class ConfigCateService
 * @package davidxu\config\services\common
 */
class ConfigCateService extends Service
{
    /**
     * @return array
     */
    public function getDropDown($app_id)
    {
        $models = ArrayHelper::itemsMerge($this->findAll($app_id));

        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');
    }

    /**
     * Get dropdown list for categories
     *
     * @param string $app_id
     * @param int|null $id
     * @return array
     */
    public function getDropDownForEdit(string $app_id, ?int $id = null)
    {
        $list = ConfigCate::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['app_id' => $app_id])
            ->andFilterWhere(['<>', 'id', $id])
            ->select(['id', 'title', 'pid', 'level'])
            ->orderBy(['sort' => SORT_ASC])
            ->asArray()
            ->all();

        $models = ArrayHelper::itemsMerge($list);
        $data = ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models), 'id', 'title');

        return ArrayHelper::merge([0 => Yii::t('configtr', 'Top category')], $data);
    }

    /**
     * 获取关联配置信息的递归数组
     *
     * @param $app_id
     * @return array
     */
    public function getItemsMergeForConfig($app_id)
    {
        return ArrayHelper::itemsMerge($this->findAllWithConfig($app_id));
    }

    /**
     * @param $cate_id
     * @return array
     */
    public function getChildIds($app_id, $cate_id)
    {
        $cates = $this->findAll($app_id);
        $cateIds = ArrayHelper::getChildIds($cates, $cate_id);
        array_push($cateIds, $cate_id);

        return $cateIds;
    }

    /**
     * 关联配置的列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAllWithConfig($app_id)
    {
        return ConfigCate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->orderBy(['sort' => SORT_ASC])
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
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll($app_id)
    {
        return ConfigCate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['app_id' => $app_id])
            ->asArray()
            ->all();
    }
}
