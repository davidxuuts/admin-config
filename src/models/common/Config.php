<?php

namespace davidxu\config\models\common;

use common\models\common\Attachment;
use davidxu\base\enums\AppIdEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\base\enums\ConfigTypeEnum;
use davidxu\config\helpers\ArrayHelper;
use davidxu\config\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "{{%common_config}}".
 *
 * @property int $id ID
 * @property string $title Title
 * @property string $name Unique name in system
 * @property string $app_id App ID
 * @property string $type Config type in Enums
 * @property int $cate_id Config category
 * @property string|null $extra Config value parameters
 * @property string|null $remark Remark
 * @property int|null $is_hide_remark Is hide remark
 * @property string|null $default_value Default config value if not set
 * @property int|null $sort Display order
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
    public static function tableName()
    {
        return '{{%common_config}}';
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['attachments'] = $this->attachments;
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'title'], 'required'],
            [['cate_id', 'is_hide_remark', 'sort', 'status'], 'integer'],
            [['status'], 'in', 'range' => StatusEnum::getKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['title', 'name'], 'string', 'max' => 50],
            [['app_id'], 'string', 'max' => 20],
            [['app_id'], 'in', 'range' => AppIdEnum::getKeys()],
            [['type'], 'in', 'range' => ConfigTypeEnum::getKeys()],
            [['extra', 'remark', 'default_value'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
            'sort' => Yii::t('configtr', 'Display order'),
            'status' => Yii::t('configtr', 'Status'),
            'created_at' => Yii::t('configtr', 'Created at'),
            'updated_at' => Yii::t('configtr', 'Updated at'),
        ];
    }

    public function getCate()
    {
        return $this->hasOne(ConfigCate::class, ['id' => 'cate_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasOne(ConfigValue::class, ['config_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        $attachments = [];
        if (ArrayHelper::isIn($this->type, ConfigTypeEnum::hasAttachment())) {
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
                }
            }
        }
        return $attachments;
    }
}
