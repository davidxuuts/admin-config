<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\models\common;

use davidxu\base\enums\AppIdEnum;
use davidxu\base\enums\BooleanEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\BaseModel;
use yii\db\ActiveQuery;
use Yii;

/**
 * This is the model class for table "{{%common_config_cate}}".
 *
 * @property int $id ID
 * @property string $title Title
 * @property int|null $pid Parent ID
 * @property string|int $app_id App ID
 * @property int|null $level Level
 * @property int|null $order Display order
 * @property int|null $dev_only Development mode only
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int|null $created_at Created at
 * @property int|null $updated_at Updated at
 *
 * @property Config[] $configs Related configurations
 * @property ConfigCate $parent
 * @property ConfigCate[] $children
 */
class ConfigCate extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%common_config_cate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title'], 'required'],
            [['pid', 'level', 'order', 'status', 'dev_only'], 'integer'],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['dev_only'], 'in', 'range' => BooleanEnum::getKeys()],
            [['dev_only'], 'default', 'value' => BooleanEnum::NO],
            [['title'], 'string', 'max' => 50],
            [['app_id'], 'string', 'max' => 20],
            [['app_id'], 'in', 'range' => AppIdEnum::getKeys()],
            [
                ['pid'], 'exist', 'skipOnError' => true,
                'targetClass' => ConfigCate::class,
                'targetAttribute' => ['pid' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('configtr', 'ID'),
            'title' => Yii::t('configtr', 'Title'),
            'pid' => Yii::t('configtr', 'Parent ID'),
            'app_id' => Yii::t('configtr', 'App ID'),
            'level' => Yii::t('configtr', 'Level'),
            'order' => Yii::t('configtr', 'Display order'),
            'dev_only' => Yii::t('configtr', 'Display on development mode'),
            'status' => Yii::t('configtr', 'Status'),
            'created_at' => Yii::t('configtr', 'Created at'),
            'updated_at' => Yii::t('configtr', 'Updated at'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getConfigs(): ActiveQuery
    {
        return $this->hasMany(Config::class, ['cate_id' => 'id'])->where(['status' => StatusEnum::ENABLED]);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return ActiveQuery
     */
    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(ConfigCate::class, ['pid' => 'id'])->orderBy(['order' => SORT_ASC]);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return ActiveQuery
     */
    public function getParent(): ActiveQuery
    {
        return $this->hasOne(ConfigCate::class, ['id' => 'pid'])->where(['not', ['pid' => NULL]]);
    }
}
