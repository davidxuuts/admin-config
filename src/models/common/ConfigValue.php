<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\models\common;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%common_config_value}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string $app_id App ID
 * @property int $config_id Config ID
 * @property string|null $data Config content
 *
 * @property Config $config
 */
class ConfigValue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%common_config_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'config_id'], 'integer'],
            [['app_id', 'config_id'], 'required'],
            [['data'], 'string'],
            [['app_id'], 'string', 'max' => 20],
            [
                ['config_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Config::class,
                'targetAttribute' => ['config_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Merchant'),
            'app_id' => Yii::t('app', 'App ID'),
            'config_id' => Yii::t('app', 'Config ID'),
            'data' => Yii::t('app', 'Config content'),
        ];
    }

    /**
     * Gets query for [[Config]].
     *
     * @return ActiveQuery
     */
    public function getConfig(): ActiveQuery
    {
        return $this->hasOne(Config::class, ['id' => 'config_id']);
    }
}
