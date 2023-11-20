<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services;

use davidxu\config\services\backend\MemberService as BackendMemberService;
use davidxu\config\services\merchant\MerchantService;
use davidxu\config\services\common\ConfigCateService;
use davidxu\config\services\common\ConfigService;

/**
 * Class Service Application
 * @package davidxu\config\services
 * @property BackendMemberService $backendMemberService
 * @property MerchantService $merchantService
 * @property ConfigService $configService
 * @property ConfigCateService $configCateService
 */
class Application extends Service
{
    /** @var array $childService */
    public array $childService = [
        'backendMemberService' => BackendMemberService::class,
        'merchantService' => MerchantService::class,
        'configService' => ConfigService::class,
        'configCateService' => ConfigCateService::class,
    ];

    /**
     * @return array|string[]
     */
    public function getServices(): array
    {
        return $this->childService;
    }
}
