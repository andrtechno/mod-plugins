<?php

namespace panix\mod\plugins\controllers\admin;

use panix\mod\plugins\models\Plugin;
use Yii;
use panix\mod\plugins\models\Event;
use panix\mod\plugins\models\search\EventSearch;
use panix\engine\controllers\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends AdminController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->pageName = Yii::t('plugins/default', 'EVENTS');
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Event::findModel($id),
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Event();
        $model->plugin_id = Plugin::EVENTS_CORE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Event::findModel($id);

        $this->pageName = Yii::t('plugins/default', 'Update {modelClass}: ', [
                'modelClass' => 'Event',
            ]) . ' ' . $model->plugin->name;
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'EVENTS'), 'url' => ['index']];
        $this->breadcrumbs[] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
        $this->breadcrumbs[] = Yii::t('plugins/default', 'Update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Event::findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
