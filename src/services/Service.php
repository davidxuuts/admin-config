<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services;

use davidxu\base\enums\AppIdEnum;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\i18n\PhpMessageSource;

/**
 * Class Service Component
 * @package davidxu\config\services
 *
 * @property-read $merchant_id
 */
class Service extends Component
{
    /** @var array $childService */
    public array $childService;
    
    /**
     * Instanced child service
     * @var array $_childService
     */
    protected array $_childService;

    /** @var int|null $merchant_id */
    protected ?int $merchant_id = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * @param string $name
     * @return object|null
     * @throws InvalidConfigException
     */
    public function __get($name)
    {
        return $this->getChildService($name);
    }

    /**
     * Get Merchant ID
     *
     * @return int|null
     */
    public function getMerchantId(): ?int
    {
        if (!$this->merchant_id) {
            $this->merchant_id = Yii::$app->services->merchantService->getId();
        }
        $appId = Yii::$app->params['appId'] ?? Yii::$app->id;
        if (in_array($appId, [AppIdEnum::CONSOLE, AppIdEnum::BACKEND])) {
            return null;
        }
        return $this->merchant_id;
    }

    /**
     * Get childService Instance
     *
     * @param string $childServiceName
     * @return object|null
     * @throws InvalidConfigException
     */
    protected function getChildService(string $childServiceName): ?object
    {
        if (!isset($this->_childService[$childServiceName])) {
            $childService = $this->childService;
            if (isset($childService[$childServiceName])) {
                $service = $childService[$childServiceName];
                $this->_childService[$childServiceName] = Yii::createObject($service);
            } else {
                throw new InvalidConfigException('Child Service [' . $childServiceName . '] is not find in '
                    . static::class . ', you must config it! ');
            }
        }
        return $this->_childService[$childServiceName] ?? null;
    }
    
    protected function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['configtr*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@davidxu/config/messages',
            'fileMap' => [
                '*' => 'configtr.php',
            ],
        ];
        $i18n->translations['base*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@davidxu/base/messages',
            'fileMap' => [
                '*' => 'base.php',
            ],
        ];
    }
}
