<?php

namespace davidxu\config\traits;

use Yii;
use yii\base\Action;
use yii\base\ExitException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\base\InvalidConfigException;
use davidxu\config\helpers\ResponseHelper;
use davidxu\base\enums\StatusEnum;
use davidxu\config\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\Response;
use yii\bootstrap4\ActiveForm;

/**
 * Trait Crud
 * @property ActiveRecord|Model $modelClass
 * @property Action $action
 * @package davidxu\config\traits
 * @method render(string $string, ActiveDataProvider[] $array)
 */
trait Crud
{
    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException(Yii::t('configtr', '{attribute} cannot be blank.', [
                'attribute' => '"modelClass"'
            ]));
        }
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * Universal action Index
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->modelClass::find(),
        ]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed Response|string
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message(
                Yii::t('configtr', 'Saved successfully'),
                $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * Action Destroy
     *
     * @param int $id
     * @return mixed
     */
    public function actionDestroy(int $id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message(
                Yii::t('configtr', 'Data not found'),
                $this->redirect(['index']),
                'error'
            );
        }

        if (isset($model->status)) {
            $model->status = StatusEnum::DELETE;
        }
        if ($model->save()) {
            return $this->message(
                Yii::t('configtr', 'Deleted successfully'),
                $this->redirect(['index']));
        }

        return $this->message(
            Json::encode($model->getFirstErrors()),
            $this->redirect(['index']),
            'error'
        );
    }

    /**
     * ajax update
     *
     * @param int $id
     * @return array
     */
    public function actionAjaxUpdate(int $id): array
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResponseHelper::json(404, Yii::t('configtr', 'Data not found'));
        }

        $model->attributes = ArrayHelper::filter(Yii::$app->request->get(), ['sort', 'status']);
        if (!$model->save()) {
            return ResponseHelper::json(422, $this->getError($model));
        }
        return ResponseHelper::json(200, Yii::t('app', 'Update successfully'), $model->attributes);
    }

    /**
     * ajax edit/create
     *
     * @return mixed|string|Response
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id', 0);
        $model = $this->findModel($id);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? $this->message(Yii::t('app', 'Saved successfully'), $this->redirect(Yii::$app->request->referrer))
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * @param Model $model
     * @return string
     */
    protected function getError(Model $model)
    {
        return $this->analysisErrors($model->getFirstErrors());
    }

    /**
     * @param int|null $id
     * @param int $merchant_id
     * @return ActiveRecord
     */
    protected function findModel(?int $id, int $merchant_id = -1): ActiveRecord
    {
        $query = $this->modelClass::find()->where(['id' => $id]);
        if ($merchant_id >= 0) {
            $query->andWhere(['merchant_id' => $merchant_id]);
        }
        /* @var $model ActiveRecord */
        if (empty($id) || ($model = $query->one()) === null) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * @return Response
     */
    protected function referrer(): Response
    {
        $key = Yii::$app->controller->route;
        $url = Yii::$app->session->get($key);
        Yii::$app->session->remove($key);
        if ($url) {
            return $this->redirect($url);
        }
        return $this->redirect(['index']);
    }

    /**
     * Common message redirect
     *
     * @param mixed $msg Message
     * @param mixed $redirectUrl Redirect URL
     * @param string|null $type Message type [success/error/info/warning]
     * @return mixed
     */
    protected function message($msg, $redirectUrl, string $type = 'success')
    {
        if (!$type || !in_array($type, ['success', 'error', 'info', 'warning'])) {
            $type = 'success';
        }
        Yii::$app->session->setFlash($type, $msg);
        return $redirectUrl;
    }

    /**
     * @param $model ActiveRecord|Model
     * @throws ExitException
     */
    protected function activeFormValidate(Model|ActiveRecord $model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = ActiveForm::validate($model);
                Yii::$app->end();
            }
        }
    }

    /**
     * Analysis Errors
     *
     * @param string|array $errors
     * @return string
     */
    public function analysisErrors($errors)
    {
        if (!is_array($errors) || empty($errors)) {
            return false;
        }

        $firstErrors = array_values($errors)[0];
        return $firstErrors ?? Yii::t('configtr', 'Error message not fount');
    }
}
