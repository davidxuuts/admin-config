<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config;

use davidxu\base\enums\AppIdEnum;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Exception;
use Yii;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class Utility
 * @package davidxu\config
 */
class Utility
{
    /**
     * @var array
     */
    protected array $config = [];

    /**
     * Return configuration name
     *
     * @param string $name Field name
     * @param bool $noCache false:Read from cache otherwise false
     * @param ?int $merchant_id Merchant ID for multiple merchants
     * @return string|null
     */
    public function config(string $name, bool $noCache = false, int $merchant_id = null): ?string
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchantService->getId();
        $app_id = !$merchant_id ? AppIdEnum::BACKEND : AppIdEnum::MERCHANT;

        // 获取缓存信息
        $info = $this->getConfigInfo($noCache, $app_id, $merchant_id);

        return isset($info[$name]) ? trim($info[$name]) : null;
    }

    /**
     * Return all configuration
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param ?int $merchant_id Merchant ID
     * @return array|mixed
     */
    public function configAll(bool $noCache = false, int $merchant_id = null): mixed
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchantService->getId();
        $app_id = !$merchant_id ? AppIdEnum::BACKEND : AppIdEnum::MERCHANT;

        $info = $this->getConfigInfo($noCache, $app_id, $merchant_id);

        return $info ?? [];
    }

    /**
     * Return backend configuration by name
     *
     * @param string $name Configuration name
     * @param bool $noCache false:Read from cache otherwise false
     * @return string|null
     */
    public function backendConfig(string $name, bool $noCache = false): ?string
    {
        // 获取缓存信息
        $info = $this->getConfigInfo($noCache, AppIdEnum::BACKEND);

        return isset($info[$name]) ? trim($info[$name]) : null;
    }

    /**
     * Return backend all configurations
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @return array|mixed
     */
    public function backendConfigAll(bool $noCache = false): mixed
    {
        $info = $this->getConfigInfo($noCache, AppIdEnum::BACKEND);

        return $info ?? [];
    }

    /**
     * Get current merchant configuration by name
     *
     * @param string $name Configuration name
     * @param bool $noCache false:Read from cache otherwise false
     * @param ?int $merchant_id Merchant ID
     * @return string|null
     */
    public function merchantConfig(string $name, bool $noCache = false, ?int $merchant_id = null): ?string
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchantService->getId();
        !$merchant_id && $merchant_id = 1;
        $info = $this->getConfigInfo($noCache, AppIdEnum::MERCHANT, $merchant_id);

        return isset($info[$name]) ? trim($info[$name]) : null;
    }

    /**
     * Get current merchant configuration
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param ?int $merchant_id Merchant ID
     * @return array|mixed
     */
    public function merchantConfigAll(bool $noCache = false, ?int $merchant_id = null): mixed
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchantService->getId();
        !$merchant_id && $merchant_id = 1;

        $info = $this->getConfigInfo($noCache, AppIdEnum::MERCHANT, $merchant_id);

        return $info ?? [];
    }

    /**
     * Get configuration by app_id
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param string $app_id AppID from AppIdEnum
     * @param ?int $merchant_id Merchant ID
     * @return array|mixed
     */
    protected function getConfigInfo(bool $noCache, string $app_id, ?int $merchant_id = null): mixed
    {
        $cacheKey = 'config:' . ($merchant_id ?? '') . $app_id;
        if (!$noCache && !empty($this->config[$cacheKey])) {
            return $this->config[$cacheKey];
        }

        if ($noCache || !($this->config[$cacheKey] = Yii::$app->cache->get($cacheKey))) {
            $config = Yii::$app->services->configService->findAllWithValue($app_id, $merchant_id);
            $this->config[$cacheKey] = [];

            foreach ($config as $row) {
                $this->config[$cacheKey][$row['name']] = $row['value']['data'] ?? $row['default_value'];
            }

            Yii::$app->cache->set($cacheKey, $this->config[$cacheKey], 60 * 60);
        }

        return $this->config[$cacheKey];
    }

//    /**
//     * 获取设备客户端信息
//     *
//     * @return mixed|string
//     */
//    public function detectVersion()
//    {
//        /** @var \Detection\MobileDetect $detect */
//        $detect = Yii::$app->mobileDetect;
//        if ($detect->isMobile()) {
//            $devices = $detect->getOperatingSystems();
//            $device = '';
//
//            foreach ($devices as $key => $valaue) {
//                if ($detect->is($key)) {
//                    $device = $key . $detect->version($key);
//                    break;
//                }
//            }
//
//            return $device;
//        }
//
//        return $detect->getUserAgent();
//    }

    /**
     * 解析系统报错
     *
     * @param Exception $e
     * @return array
     */
    public function getSysError(Exception $e): array
    {
        return [
            'errorMessage' => $e->getMessage(),
            'type' => get_class($e),
            'file' => method_exists($e, 'getFile') ? $e->getFile() : '',
            'line' => $e->getLine(),
            'stack-trace' => explode("\n", $e->getTraceAsString()),
        ];
    }

    /**
     * Gets wechat error info
     * @param array $message
     * @param bool $direct
     * @return bool
     * @throws UnprocessableEntityHttpException
     */
    public function getWechatError(array $message, bool $direct = true): bool
    {
        if (isset($message['errcode']) && $message['errcode'] != 0) {
            if ($message['errcode'] == 40001) {
                Yii::$app->wechat->app->access_token->getToken(true);
            }
            if ($direct) {
                throw new UnprocessableEntityHttpException($message['errmsg']);
            }
            return $message['errmsg'];
        }
        return false;
    }

    /**
     * 解析错误
     *
     * @param array|string $firstErrors
     * @return bool|string
     */
    public function analyErr(array|string $firstErrors): bool|string
    {
        if (!is_array($firstErrors) || empty($firstErrors)) {
            return false;
        }

        $errors = array_values($firstErrors)[0];
        return $errors ?? '未捕获到错误信息';
    }
}