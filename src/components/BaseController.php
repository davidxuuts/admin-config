<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\components;

use davidxu\base\actions\backend\AjaxEditAction;
use davidxu\base\actions\backend\DestroyAction;
use davidxu\base\actions\backend\EditAction;
use davidxu\base\actions\backend\IndexAction;
use davidxu\base\actions\backend\DeleteAction;
use davidxu\base\actions\backend\SortOrderAction;
use davidxu\config\helpers\ArrayHelper;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * @property string|ActiveRecordInterface|null $modelClass
 */
class BaseController extends Controller
{
    public array $allowAction = [];
    /**
     * @var string|ActiveRecordInterface|null
     */
    public ActiveRecordInterface|string|null $modelClass = null;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        return ArrayHelper::merge([
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => $this->modelClass,
            ],
            'edit' => [
                'class' => EditAction::class,
                'modelClass' => $this->modelClass,
            ],
            'ajax-edit' => [
                'class' => AjaxEditAction::class,
                'modelClass' => $this->modelClass,
            ],
            'destroy' => [
                'class' => DestroyAction::class,
                'modelClass' => $this->modelClass,
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => $this->modelClass,
            ],
            'sort-order' => [
                'class' => SortOrderAction::class,
                'modelClass' => $this->modelClass,
            ],
        ], $actions);
    }

    /**
     * @param int|string|null $id
     * @param int|null $merchant_id
     * @param string|ActiveRecordInterface|null $modelClass
     * @return ActiveRecordInterface|ActiveRecord|Model
     */
    protected function findModel(int|string|null $id, string|ActiveRecordInterface $modelClass = null,
                                 ?int $merchant_id = null): ActiveRecordInterface|ActiveRecord|Model
    {
        /* @var $modelClass ActiveRecordInterface|Model|ActiveRecord */
        if (!$modelClass) {
            $modelClass = $this->modelClass;
        }
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        } elseif ($modelClass::findOne($id) === null) {
            $model = new $modelClass;
        }

        if (!isset($model)) {
            $model = new $modelClass;
        }
        $model->loadDefaultValues();
        return $model;
    }
}
