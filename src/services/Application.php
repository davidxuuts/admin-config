<?php

namespace davidxu\config\services;

use davidxu\config\services\backend\MemberService as BackendMemberService;
use davidxu\config\services\merchant\MerchantService;
use davidxu\config\services\common\ConfigCateService;
use davidxu\config\services\common\ConfigService;

/**
 * Class Service Application
 * @package davidxu\config\services
 * @property BackendMemberService $backendMember
 * @property MerchantService $merchant
 * @property ConfigService $config
 * @property ConfigCateService $configCate
 */
class Application extends Service
{
    /** @var array $childService */
    public array $childService = [
        'backendMember' => BackendMemberService::class,
        'merchant' => MerchantService::class,
        'config' => ConfigService::class,
        'configCate' => ConfigCateService::class,
    ];
}
