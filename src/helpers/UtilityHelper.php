<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\helpers;

use davidxu\base\models\Attachment;
use davidxu\base\enums\ConfigTypeEnum;
use davidxu\config\models\common\Config;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;

class UtilityHelper
{
    /**
     * @param array|Config|ActiveRecord $config
     * @param string|ActiveRecord|ActiveRecordInterface $modelClass
     * @return array
     */
    public static function getConfigAttachments(array|Config|ActiveRecord $config, string|ActiveRecord|ActiveRecordInterface $modelClass = Attachment::class): array
    {
        $attachments = [];
        if (ArrayHelper::isIn($config->type, ConfigTypeEnum::hasAttachment())) {
            $allAttachments = [];
            if ($config->value && $config->value->data) {
                $value = $config->value->data;
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
                $allAttachments = $modelClass::findAll($value);
            } elseif ($config->default_value) {
                $value = $config->default_value;
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
                $allAttachments = $modelClass::findAll($value);
            }

            if ($allAttachments) {
                foreach ($allAttachments as $attachment) {
                    /** @var Attachment $attachment */
                    $attach = [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'path' => $attachment->path,
                        'size' => $attachment->size,
                        'file_type' => $attachment->file_type,
                        'poster' => $attachment->poster ?? '/images/default-video.jpg',
                    ];
                    $attachments[] = $attach;
                }
            }
        }
        return $attachments;
    }
//    /**
//     * @param array|Config|ActiveRecord $config
//     * @param string|ActiveRecord|ActiveRecordInterface $modelClass
//     * @return array
//     */
//    public static function getConfigAttachments(array|Config|ActiveRecord $config, string|ActiveRecord|ActiveRecordInterface $modelClass = Attachment::class): array
//    {
//        $attachments = [];
//        if (ArrayHelper::isIn($config->type, ConfigTypeEnum::hasAttachment())) {
//            if (isset($row['value']) && isset($row['value']['data'])) {
//                $value = $row['value']['data'];
//                if (is_string($value)) {
//                    $value = explode(',', $value);
//                }
//                $allAttachments = $modelClass::findAll(['id' => $value]);
//            } elseif ($row['default_value']) {
//                if (is_string($row['default_value'])) {
//                    $value = explode(',', $row['default_value']);
//                }
//                $allAttachments = $modelClass::findAll(['id' => $value]);
//            } else {
//                $allAttachments = [];
//            }
//            if ($allAttachments) {
//                foreach ($allAttachments as $attachment) {
//                    /** @var Attachment $attachment */
//                    $attach = [
//                        'id' => $attachment->id,
//                        'name' => $attachment->name,
//                        'path' => $attachment->path,
//                        'size' => $attachment->size,
//                        'file_type' => $attachment->file_type,
//                        'poster' => $attachment->poster ?? '/images/default-video.jpg',
//                    ];
//                    $attachments[] = $attach;
//                }
//            }
//        }
//        return $attachments;
//    }
}
