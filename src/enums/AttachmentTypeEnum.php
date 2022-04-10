<?php

namespace davidxu\config\enums;
use Yii;

/**
 * AttachmentType Enum
 *
 * Class AttachmentTypeEnum
 * @package davidxu\config\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class AttachmentTypeEnum extends BaseEnum
{
    public const TYPE_IMAGES = 'images';
    public const TYPE_VIDEOS = 'videos';
    public const TYPE_VOICES = 'voices';
    public const TYPE_NEWS   = 'news';
    public const TYPE_THUMBNAILS = 'thumbs';
    public const TYPE_OTHERS = 'others';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TYPE_IMAGES => Yii::t('uploadtr', 'Images'),
            self::TYPE_VIDEOS => Yii::t('uploadtr', 'Videos'),
            self::TYPE_VOICES => Yii::t('uploadtr', 'Voices'),
            self::TYPE_NEWS => Yii::t('uploadtr', 'Hybrids'),
            self::TYPE_THUMBNAILS => Yii::t('uploadtr', 'Thumbnails'),
            self::TYPE_OTHERS => Yii::t('uploadtr', 'Others'),
        ];
    }
}
