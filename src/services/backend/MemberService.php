<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services\backend;

use davidxu\config\models\backend\Member;
use davidxu\base\enums\StatusEnum;
use davidxu\config\models\base\User;
use davidxu\config\services\Service;
use davidxu\srbac\models\Assignment;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use Yii;

class MemberService extends Service
{
    public ActiveRecord|ActiveRecordInterface|string|null $modelClass = null;

    public function init()
    {
        parent::init();
        $this->modelClass = Yii::$app->getUser()->identityClass ?? User::class;
    }

    /**
     * Record visit count
     * @param Member $member
     * @return void
     */
    public function lastLogin(Member $member): void
    {
        ++$member->visit_count;
        $member->last_time = time();
        $member->last_ip = Yii::$app->request->getUserIP();
        $member->save();
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return ArrayHelper::map($this->findAll(), 'id', 'username');
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->modelClass::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }

    /**
     * @param int $id
     * @return array|ActiveRecord|null
     */
    public function findByIdWithAssignment(int $id): array|ActiveRecord|null
    {
        return $this->modelClass::find()
            ->where(['id' => $id])
//            ->with('assignment')
            ->one();
    }

    /**
     * @param int|null $id
     * @return array|ActiveRecord|null
     */
    public function findById(?int $id): array|ActiveRecord|null
    {
        return $this->modelClass::find()
            ->where(['id' => $id])
            ->one();
    }

    /**
     * @param int $id
     * @param string $type
     * @return array|ActiveRecord[]
     */
    public function getRoles(int $id, string $type = 'array'): array
    {
       $roles = Assignment::find()->where(['user_id' => $id])->all();
        if ($type === 'array') {
            $items = [];
            if ($roles) {
                foreach ($roles as $role) {
                    $items[] = $role->item_name;
                }
            }
            return $items;
       } else {
            return $roles;
        }
    }
}

