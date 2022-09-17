<?php

namespace davidxu\config\helpers;

use davidxu\base\enums\StatusEnum;
use davidxu\sweetalert2\SweetAlert2;
use yii\bootstrap4\BaseHtml;
use Yii;
use yii\web\JsExpression;

class Html extends BaseHtml
{
    /**
     * Status tag
     *
     * @param int $status
     * @return mixed
     */
    public static function status(int $status = StatusEnum::ENABLED, $options = [])
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

    public static function delete(int $id, $options = [])
    {
        return self::a('<i class="fas fa-trash-alt"></i>', ['delete', 'id' => $id], array_merge([
            'title' => Yii::t('configtr', 'Delete'),
            'aria-label' => Yii::t('configtr', 'Delete'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
        ], $options));
    }

    public static function destroy(int $id, $options = [])
    {
        return self::a('<i class="fas fa-trash-alt"></i>', ['destroy', 'id' => $id], array_merge([
            'title' => Yii::t('configtr', 'Delete'),
            'aria-label' => Yii::t('configtr', 'Delete'),
            'data-method' => 'post',
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
        ], $options));
    }

    public static function sort(int $value, $options = [])
    {
        $options = ArrayHelper::merge([
            'data-message' => Yii::t('configtr', 'Only number allowed for display order'),
            'class' => 'form-control form-control-sm sort-input',
            'onblur' => 'editOrder(this)',
        ], $options);

        return self::input('number', 'sort', $value, $options);
    }
}
