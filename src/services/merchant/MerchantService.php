<?php

namespace davidxu\config\services\merchant;

use davidxu\config\services\Service;
use common\enums\MerchantStatusEnum;
use davidxu\base\enums\StatusEnum;
use common\models\merchant\Merchant;
use yii\db\ActiveRecord;

/**
 *
 * Class MerchantService
 * @package davidxu\config\services\merchant
 *
 * @property int $merchant_id Merchant ID
 */
class MerchantService extends Service
{
    /**
     * @var int
     */
    protected $merchant_id = 1;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->merchant_id;
    }

    /**
     * @param int $merchant_id
     */
    public function setId(int $merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @return int
     */
    public function getNotNullId(): int
    {
        return !empty($this->merchant_id) ? (int)$this->merchant_id : 0;
    }

    /**
     * @param int $merchant_id
     */
    public function addId(int $merchant_id)
    {
        !$this->merchant_id && $this->merchant_id = $merchant_id;
    }

    /**
     * @param int $merchant_id
     * @return mixed
     */
    public function getCount(int $merchant_id = 1)
    {
        return Merchant::find()
            ->select('id')
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['state' => StatusEnum::ENABLED])
            ->andFilterWhere(['id' => $merchant_id])
            ->count();
    }

    /**
     * @return int|string
     */
    public function getApplyCount($merchant_id = '')
    {
        return Merchant::find()
            ->select('id')
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['in', 'state', [MerchantStatusEnum::AUDIT]])
            ->andFilterWhere(['id' => $merchant_id])
            ->count();
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function findByLogin()
    {
        return $this->findById($this->getId());
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function findById($id)
    {
        return Merchant::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @return array|ActiveRecord|null
     */
    public function findBaseById($id)
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
     * @return array|ActiveRecord|null
     */
    public function findBaseByIds($ids)
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
     * @return array|ActiveRecord|null
     */
    public function findBaseAll()
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
