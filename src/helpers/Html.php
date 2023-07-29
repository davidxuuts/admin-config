<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\helpers;

use davidxu\base\enums\StatusEnum;
use yii\bootstrap4\BaseHtml;
use Yii;
use yii\helpers\Url;

class Html extends BaseHtml
{
    public static function displayStatus(int $status = StatusEnum::ENABLED, $options = [])
    {
        $listBut = [
            StatusEnum::DISABLED => self::button(StatusEnum::getValue($status), array_merge(
                [
                    'class' => 'btn btn-xs btn-outline-secondary',
                ], $options
            )),
            StatusEnum::ENABLED => self::button(StatusEnum::getValue($status), array_merge(
                [
                    'class' => 'btn btn-xs btn-outline-success',
                ], $options
            )),
        ];

        return $listBut[$status] ?? '';
    }

    /**
     * Status tag
     *
     * @param int $status
     * @param array|null $options
     * @return mixed
     */
    public static function status(int $status = StatusEnum::ENABLED, ?array $options = []): mixed
    {
        $listBut = [
            StatusEnum::DISABLED => self::tag('a', '<i class="fas fa-ban"></i>', array_merge(
                [
                    'class' => 'btn-outline-danger',
                    'title' => Yii::t('configtr', 'Enable'),
                    'aria-label' => Yii::t('configtr', 'Enable'),
                    'onclick' => "editStatus(this)"
                ], $options
            )),
            StatusEnum::ENABLED => self::tag('a', '<i class="fas fa-check-circle"></i>', array_merge(
                [
                    'class' => 'btn-outline-success',
                    'title' => Yii::t('configtr', 'Disable'),
                    'aira-label' => Yii::t('configtr', 'Disable'),
                    'onclick' => "editStatus(this)"
                ], $options
            )),
        ];

        return $listBut[$status] ?? '';
    }

    /**
     * @param int|string $id
     * @param array|null $options
     * @return string
     */
    public static function delete(int|string $id, ?array $options = []): string
    {
        return self::a('<i class="fas fa-trash-alt"></i>', ['delete', 'id' => $id], array_merge([
            'title' => Yii::t('configtr', 'Delete'),
            'aria-label' => Yii::t('configtr', 'Delete'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
        ], $options));
    }

    /**
     * @param int|string $id
     * @param array|null $options
     * @return string
     */
    public static function destroy(int|string $id, ?array $options = []): string
    {
        return self::a('<i class="fas fa-trash-alt"></i>', ['destroy', 'id' => $id], array_merge([
            'title' => Yii::t('configtr', 'Delete'),
            'aria-label' => Yii::t('configtr', 'Delete'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
        ], $options));
    }

    /**
     * @param int|string $value
     * @param array|null $options
     * @return string
     */
    public static function sort(int|string $value, ?array $options = []): string
    {
        $options = ArrayHelper::merge([
            'data-message' => Yii::t('configtr', 'Only number allowed for display order'),
            'class' => 'form-control form-control-sm sort-input',
            'data-current-url' => Url::current(),
            'onblur' => 'editOrder(this)',
        ], $options);

        return self::input('number', 'order', $value, $options);
    }
}
