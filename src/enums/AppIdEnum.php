<?php

namespace davidxu\config\enums;

/**
 * AppId Enum
 *
 * Class AppIdEnum
 * @package davidxu\config\enums
 * @author David Xu <david.xu.uts@163.com>
 */
class AppIdEnum extends BaseEnum
{

    const BACKEND = 0;
    const FRONTEND = 1;
    const API = 2;
    const HTML5 = 3;
    const MERCHANT = 4;

    /**
     * @inheritDoc
     */
    public static function getMap(): array
    {
        return [
            self::BACKEND => 'BACKEND',
            self::FRONTEND => 'FRONTEND',
            self::API => 'API',
            self::HTML5 => 'HTML5',
            self::MERCHANT => 'MERCHANT',
        ];
    }

    public static function api(): array
    {
        return [
            self::API,
        ];
    }
}
