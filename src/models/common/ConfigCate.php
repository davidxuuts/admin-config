<?php

namespace davidxu\config\models\common;

use davidxu\base\enums\AppIdEnum;
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
 * @property string $app_id App ID
 * @property int|null $level Level
 * @property int|null $sort Display order
 * @property int|null $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int|null $created_at Created at
 * @property int|null $updated_at Updated at
 *
 * @property Config[] $configs Related configurations
 */
class ConfigCate extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%common_config_cate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['pid', 'level', 'sort'], 'integer'],
            [['status'], 'in', 'range' => StatusEnum::getKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['title'], 'string', 'max' => 50],
            [['app_id'], 'string', 'max' => 20],
            [['app_id'], 'in', 'range' => AppIdEnum::getKeys()],
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
            'pid' => Yii::t('configtr', 'Parent ID'),
            'app_id' => Yii::t('configtr', 'App ID'),
            'level' => Yii::t('configtr', 'Level'),
            'sort' => Yii::t('configtr', 'Display order'),
            'status' => Yii::t('configtr', 'Status'),
            'created_at' => Yii::t('configtr', 'Created at'),
            'updated_at' => Yii::t('configtr', 'Updated at'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasMany(Config::class, ['cate_id' => 'id']);
    }
}
