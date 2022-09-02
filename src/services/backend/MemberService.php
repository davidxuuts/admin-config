<?php

namespace davidxu\config\services\backend;

use davidxu\config\models\backend\Member;
use davidxu\base\enums\StatusEnum;
use davidxu\config\services\Service;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class MemberService extends Service
{
    /**
     * Record visit count
     * @param Member $member
     * @return void
     */
    public function lastLogin(Member $member)
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
        return Member::find()
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
        return Member::find()
            ->where(['id' => $id])
//            ->with('assignment')
            ->one();
    }
}
