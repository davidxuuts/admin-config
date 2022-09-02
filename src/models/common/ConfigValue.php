<?php

namespace davidxu\config\models\common;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%common_config_value}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id Merchant
 * @property string $app_id App ID
 * @property int $config_id Config ID
 * @property string|null $data Config content
 */
class ConfigValue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%common_config_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'config_id'], 'integer'],
            [['data'], 'string'],
            [['app_id'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Merchant'),
            'app_id' => Yii::t('app', 'App ID'),
            'config_id' => Yii::t('app', 'Config ID'),
            'data' => Yii::t('app', 'Config content'),
        ];
    }
}
