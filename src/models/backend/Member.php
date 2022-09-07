<?php

namespace davidxu\config\models\backend;

use davidxu\base\enums\GenderEnum;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\User;
use Yii;
use yii\base\Exception;
//use common\models\rbac\AuthAssignment;

/**
 * This is the model class for table "{{%backend_member}}".
 *
 * @property int $id ID
 * @property string $username Username
 * @property string $password_hash Password
 * @property string $auth_key Authorization key
 * @property string $password_reset_token Password reset token
 * @property int $type Type[1:Admin, 10:Super admin]
 * @property string $realname Real name
 * @property string $head_portrait Head portrait
 * @property int $gender Gender[0:Unknown;1:Male;2:Female]
 * @property string $qq QQ
 * @property string $email Email
 * @property string $birthday Birthday
 * @property string $province_id Province
 * @property string $city_id City
 * @property string $district_id District
 * @property string $address Detail address
 * @property string $mobile Mobile
 * @property string $tel Tel
 * @property string $wechat_openid Wechat OpenID
 * @property string $install_robot_token DingTalk token
 * @property int $visit_count Visit count
 * @property int $last_time Last visit time
 * @property string $last_ip Last visit IP
 * @property int $role Role
 * @property int $status Status[-1:Deleted;0:Disabled;1:Enabled]
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 *
 */
class Member extends User
{
    public const SCENARIO_BACKEND_UPDATE = 'SCENARIO_BACKEND_UPDATE';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%backend_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'password_hash', 'auth_key'], 'required'],
            [
                ['type', 'gender', 'visit_count', 'last_time', 'role', 'status'],
                'integer'
            ],
            [['province_id', 'city_id', 'district_id'], 'string', 'max' => 6, 'min' => 6],
            [['gender'], 'in', 'range' => GenderEnum::getKeys()],
            [['gender'], 'default', 'value' => GenderEnum::UNKNOWN],
            [['status'], 'in', 'range' => StatusEnum::getKeys()],
            [['status'], 'default', 'value' => StatusEnum::ENABLED],
            [['gender', 'wechat_openid'], 'required', 'on' => self::SCENARIO_BACKEND_UPDATE],
            [['birthday'], 'safe'],
            [['username', 'realname', 'qq', 'mobile', 'tel'], 'string', 'max' => 20],
            [['password_hash', 'password_reset_token', 'head_portrait'], 'string', 'max' => 150],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 60],
            [['email'], 'email'],
            [['address', 'dingtalk_robot_token', 'wechat_openid'], 'string', 'max' => 100],
            [['wechat_openid'], 'unique'],
            [['last_ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('configtr', 'Username'),
            'password_hash' => Yii::t('configtr', 'Password'),
            'auth_key' => Yii::t('configtr', 'Authorization key'),
            'password_reset_token' =>Yii::t('configtr', 'Password reset token'),
            'type' => Yii::t('configtr', 'Type'),
            'realname' => Yii::t('configtr', 'Real name'),
            'head_portrait' => Yii::t('configtr', 'Head portrait'),
            'gender' => Yii::t('configtr', 'Gender'),
            'qq' => Yii::t('configtr','QQ'),
            'email' => Yii::t('configtr', 'Email'),
            'birthday' => Yii::t('configtr', 'Birthday'),
            'province_id' => Yii::t('configtr', 'Province'),
            'city_id' => Yii::t('configtr', 'City'),
            'district_id' => Yii::t('configtr', 'District'),
            'address' => Yii::t('configtr', 'Detail address'),
            'mobile' => Yii::t('configtr', 'Mobile'),
            'tel' => Yii::t('configtr', 'Tel'),
            'dingtalk_robot_token' => Yii::t('configtr', 'DingTalk robot token'),
            'wechat_openid' => Yii::t('configtr', 'Wechat account/openid'),
            'visit_count' => Yii::t('configtr', 'Visit count'),
            'last_time' => Yii::t('configtr', 'Last visit time'),
            'last_ip' => Yii::t('configtr', 'Last visit IP'),
            'role' => Yii::t('configtr', 'Role'),
            'status' => Yii::t('configtr', 'Status'),
            'created_at' => Yii::t('configtr', 'Created at'),
            'updated_at' => Yii::t('configtr', 'Updated at'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if ($this->isNewRecord) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
//    public function beforeDelete(): bool
//    {
//        AuthAssignment::deleteAll(['user_id' => $this->id, 'app_id' => AppEnum::BACKEND]);
//        return parent::beforeDelete();
//    }
}
