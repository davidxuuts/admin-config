<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services\merchant;

use davidxu\config\services\Service;
use davidxu\base\enums\MerchantStatusEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\merchant\Merchant;
use yii\db\ActiveRecord;

/**
 *
 * Class MerchantService
 * @package davidxu\config\services\merchant
 *
 * @property int|null $merchant_id Merchant ID
 */
class MerchantService extends Service
{
    /** @var int|null  */
    protected ?int $merchant_id = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->merchant_id;
    }

    /**
     * @param int|null $merchant_id
     */
    public function setId(?int $merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @param int|null $merchant_id
     * @return int|string|null
     */
    public function getCount(?int $merchant_id = null): int|string|null
    {
        return Merchant::find()
            ->select('id')
            ->where(['state' => StatusEnum::ENABLED])
            ->andFilterWhere(['id' => $merchant_id])
            ->count();
    }

    /**
     * @param int|null $merchant_id
     * @return int|string|null
     */
    public function getApplyCount(?int $merchant_id = null): int|string|null
    {
        return Merchant::find()
            ->select('id')
            ->andWhere(['in', 'state', [MerchantStatusEnum::AUDIT]])
            ->andFilterWhere(['id' => $merchant_id])
            ->count();
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function findByLogin(): array|ActiveRecord|null
    {
        return $this->findById($this->getId());
    }

    /**
     * @param int|string|null $id
     * @return array|ActiveRecord|null
     */
    public function findById(int|string|null $id): array|ActiveRecord|null
    {
        return Merchant::find()
            ->where(['status' => StatusEnum::DISABLED])
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @param string|int $id
     * @return array|ActiveRecord|null
     */
    public function findBaseById(string|int $id): array|ActiveRecord|null
    {
        return Merchant::find()
            ->select([
                'id',
                'title',
                'cover',
                'address_name',
                'address_details',
                'longitude',
                'latitude',
            ])
            ->where(['id' => $id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->one();
    }

    /**
     * @param array $ids
     * @return array
     */
    public function findBaseByIds(array $ids): array
    {
        return Merchant::find()
            ->select([
                'id',
                'title',
                'cover',
                'address_name',
                'address_details',
                'longitude',
                'latitude',
            ])
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['in', 'id', $ids])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function findBaseAll(): array
    {
        return Merchant::find()
            ->select([
                'id',
                'title',
                'cover',
                'address_name',
                'address_details',
                'longitude',
                'latitude',
            ])
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->asArray()
            ->all();
    }
}
