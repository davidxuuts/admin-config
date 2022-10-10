<?php

namespace davidxu\config\helpers;

use davidxu\base\models\Attachment;
use davidxu\base\enums\ConfigTypeEnum;
use yii\db\ActiveRecord;

class UtilityHelper
{
    /**
     * @param array $row
     * @param ActiveRecord $modelClass
     * @return array
     */
    public static function getConfigAttachments($row, $modelClass = Attachment::class)
    {
        $attachments = [];
        if (ArrayHelper::isIn($row['type'], ConfigTypeEnum::hasAttachment())) {
            if (isset($row['value']) && isset($row['value']['data'])) {
                $value = $row['value']['data'];
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
                $allAttachments = $modelClass::findAll(['id' => $value]);
            } elseif ($row['default_value']) {
                if (is_string($row['default_value'])) {
                    $value = explode(',', $row['default_value']);
                }
                $allAttachments = $modelClass::findAll(['id' => $value]);
            } else {
                $allAttachments = [];
            }
            if ($allAttachments) {
                foreach ($allAttachments as $attachment) {
                    /** @var Attachment $attachment */
                    $attach = [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'path' => $attachment->path,
                        'size' => $attachment->size,
                    ];
                    $attachments[] = $attach;
                }
            }
        }
        return $attachments;
    }
}
