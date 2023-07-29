<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\models\common;

use davidxu\base\models\Attachment;
use davidxu\base\enums\AppIdEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\base\enums\ConfigTypeEnum;
use davidxu\config\helpers\ArrayHelper;
use davidxu\config\models\base\BaseModel;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%common_config}}".
 *
 * @property int $id ID
 * @property string $title Title
 * @property string $name Unique name in system
 * @property string|int $app_id App ID
 * @property string $type Config type in Enums
 * @property int $cate_id Config category
 * @property string|null $extra Config value parameters
 * @property string|null $remark Remark
 * @property int|null $is_hide_remark Is hide remark
 * @property string|null $default_value Default config value if not set
 * @property int|null $order Display order
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int|null $created_at Created at
 * @property int|null $updated_at Updated at
 *
 * @property-read ConfigCate $cate Category
 * @property-read ConfigValue $value Config value
 * @property-read Attachment[] $attachments Attachments
 */
class Config extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%common_config}}';
    }

    public function fields(): array
    {
        $fields = parent::fields();
        $fields['attachments'] = $this->attachments;
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'type', 'title', 'cate_id', 'app_id'], 'required'],
            [['cate_id', 'is_hide_remark', 'order', 'status'], 'integer'],
            [['status'], 'in', 'range' => StatusEnum::getBoolKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['title', 'name'], 'string', 'max' => 50],
            [['app_id'], 'string', 'max' => 20],
            [['app_id'], 'in', 'range' => AppIdEnum::getKeys()],
            [['type'], 'in', 'range' => ConfigTypeEnum::getKeys()],
            [['extra', 'remark', 'default_value'], 'string', 'max' => 1000],
            [['name', 'app_id'], 'unique', 'targetAttribute' => ['name', 'app_id']],
            [
                ['cate_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConfigCate::class,
                'targetAttribute' => ['cate_id' => 'id']
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
            'name' => Yii::t('configtr', 'Config name'),
            'app_id' => Yii::t('configtr', 'App ID'),
            'type' => Yii::t('configtr', 'Config type'),
            'cate_id' => Yii::t('configtr', 'Category'),
            'extra' => Yii::t('configtr', 'Config parameters'),
            'remark' => Yii::t('configtr', 'Remark'),
            'is_hide_remark' => Yii::t('configtr', 'Hide remark'),
            'default_value' => Yii::t('configtr', 'Default value'),
            'order' => Yii::t('configtr', 'Display order'),
            'status' => Yii::t('configtr', 'Status'),
            'created_at' => Yii::t('configtr', 'Created at'),
            'updated_at' => Yii::t('configtr', 'Updated at'),
        ];
    }

    /**
     * Gets query for [[Cate]]
     * @return ActiveQuery
     */
    public function getCate(): ActiveQuery
    {
        return $this->hasOne(ConfigCate::class, ['id' => 'cate_id']);
    }

    /**
     * Gets query for [[Value]]
     * @return ActiveQuery
     */
    public function getValue(): ActiveQuery
    {
        return $this->hasOne(ConfigValue::class, ['config_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        $attachments = [];
        if (ArrayHelper::isIn($this->type, ConfigTypeEnum::hasAttachment())) {
            $allAttachments = null;
            if ($this->value) {
                if (is_string($this->value)) {
                    $value = explode(',', $this->value);
                }
                $allAttachments = Attachment::findAll(['id' => $this->value]);
            } elseif ($this->default_value) {
                if (is_string($this->default_value)) {
                    $value = explode(',', $this->default_value);
                }
                $allAttachments = Attachment::findAll(['id' => $this->default_value]);
            }
            if ($allAttachments) {
                foreach ($allAttachments as $attachment) {
                    $attachments[]['id'] = $attachment->id;
                    $attachments[]['name'] = $attachment->name;
                    $attachments[]['path'] = $attachment->path;
                    $attachments[]['size'] = $attachment->size;
                    $attachments[]['file_type'] = $attachment->file_type;
                    $attachments[]['poster'] = $attachment->poster ?? '/images/default-video.jpg';
                }
            }
        }
        return $attachments;
    }
}
