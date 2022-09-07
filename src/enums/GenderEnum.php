<?php

namespace davidxu\config\enums;

use Yii;
use davidxu\config\enums\BaseEnum;

/**
 * Gender Enum
 *
 * Class GenderEnum
 * @package davidxu\config\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class GenderEnum extends BaseEnum
{
    public const UNKNOWN = 0;
    public const MALE = 1;
    public const FEMALE = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MALE => Yii::t('configtr', 'Male'),
            self::FEMALE => Yii::t('configtr', 'Female'),
            self::UNKNOWN => Yii::t('configtr', 'Unknown'),
        ];
    }
}
