<?php

namespace davidxu\config;

use davidxu\base\enums\AppIdEnum;
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
     * @param int $merchant_id Merchant ID for multiple merchants
     * @return string|null
     */
    public function config(string $name, bool $noCache = false, int $merchant_id = 0)
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchant->getId();
        $app_id = !$merchant_id ? AppIdEnum::BACKEND : AppIdEnum::MERCHANT;

        // 获取缓存信息
        $info = $this->getConfigInfo($noCache, $app_id, $merchant_id);

        return isset($info[$name]) ? trim($info[$name]) : null;
    }

    /**
     * Return all configuration
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param int $merchant_id Merchant ID
     * @return array|mixed
     */
    public function configAll($noCache = false, int $merchant_id = 0)
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchant->getId();
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
    public function backendConfig(string $name, bool $noCache = false)
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
    public function backendConfigAll(bool $noCache = false)
    {
        $info = $this->getConfigInfo($noCache, AppIdEnum::BACKEND);

        return $info ?? [];
    }

    /**
     * Get current merchant configuration by name
     *
     * @param string $name Configuration name
     * @param bool $noCache false:Read from cache otherwise false
     * @param int $merchant_id Merchant ID
     * @return string|null
     */
    public function merchantConfig(string $name, $noCache = false, $merchant_id = 0)
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchant->getId();
        !$merchant_id && $merchant_id = 1;
        $info = $this->getConfigInfo($noCache, AppIdEnum::MERCHANT, $merchant_id);

        return isset($info[$name]) ? trim($info[$name]) : null;
    }

    /**
     * Get current merchant configuration
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param int $merchant_id Merchant ID
     * @return array|mixed
     */
    public function merchantConfigAll(bool $noCache = false, int $merchant_id = 0)
    {
        !$merchant_id && $merchant_id = Yii::$app->services->merchant->getId();
        !$merchant_id && $merchant_id = 1;

        $info = $this->getConfigInfo($noCache, AppIdEnum::MERCHANT, $merchant_id);

        return $info ?? [];
    }

    /**
     * Get configuration by app_id
     *
     * @param bool $noCache false:Read from cache otherwise false
     * @param string $app_id AppID from AppIdEnum
     * @param int $merchant_id Merchant ID
     * @return array|mixed
     */
    protected function getConfigInfo(bool $noCache, string $app_id, int $merchant_id = 0)
    {
        $cacheKey = 'config:' . $merchant_id . $app_id;
        if (!$noCache && !empty($this->config[$cacheKey])) {
            return $this->config[$cacheKey];
        }

        if ($noCache || !($this->config[$cacheKey] = Yii::$app->cache->get($cacheKey))) {
            $config = Yii::$app->services->config->findAllWithValue($app_id, $merchant_id);
            $this->config[$cacheKey] = [];

            foreach ($config as $row) {
                $this->config[$cacheKey][$row['name']] = $row['value']['data'] ?? $row['default_value'];
            }

            Yii::$app->cache->set($cacheKey, $this->config[$cacheKey], 60 * 60);
        }

        return $this->config[$cacheKey];
    }

    /**
     * 获取设备客户端信息
     *
     * @return mixed|string
     */
    public function detectVersion()
    {
        /** @var \Detection\MobileDetect $detect */
        $detect = Yii::$app->mobileDetect;
        if ($detect->isMobile()) {
            $devices = $detect->getOperatingSystems();
            $device = '';

            foreach ($devices as $key => $valaue) {
                if ($detect->is($key)) {
                    $device = $key . $detect->version($key);
                    break;
                }
            }

            return $device;
        }

        return $detect->getUserAgent();
    }

    /**
     * 解析系统报错
     *
     * @param \Exception $e
     * @return array
     */
    public function getSysError(\Exception $e)
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
     * 解析微信是否报错
     *
     * @param array $message 微信回调数据
     * @param bool $direct 是否直接报错
     * @return bool
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getWechatError($message, $direct = true)
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
     * @param $fistErrors
     * @return string
     */
    public function analyErr($firstErrors)
    {
        if (!is_array($firstErrors) || empty($firstErrors)) {
            return false;
        }

        $errors = array_values($firstErrors)[0];
        return $errors ?? '未捕获到错误信息';
    }
}