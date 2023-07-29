<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\services\common;

use davidxu\config\services\Service;
use Yii;

class PluginService extends Service
{
    public function getLocalList(): array
    {
        $addonDir = Yii::getAlias('@addons');

        // 获取插件列表
        $dirs = array_map('basename', glob($addonDir . '/*'));
        $list = Addons::find()
            ->where(['in', 'name', $dirs])
            ->asArray()
            ->all();

        $tmpAddons = [];
        foreach ($list as $addon) {
            $tmpAddons[$addon['name']] = $addon;
        }

        $addons = [];
        foreach ($dirs as $value) {
            // 判断是否安装
            if (!isset($tmpAddons[$value])) {
                $class = AddonHelper::getAddonConfig($value);

                // 实例化插件失败忽略执行
                if (class_exists($class)) {
                    $config = new $class;
                    $addons[$value] = $config->info;
                }
            }
        }

        return $addons;
    }
}
