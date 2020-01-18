<?php

namespace panix\mod\plugins\controllers\admin;

use panix\mod\plugins\models\search\ShortcodeSearch;
use Yii;
use panix\mod\plugins\models\Shortcode;
use panix\engine\controllers\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Class ShortcodeController
 * @package panix\mod\plugins\controllers
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ShortcodeController extends AdminController
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
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShortcodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->pageName = Yii::t('plugins/default', 'Shortcodes');
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shortcode model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Shortcode::findModel($id),
        ]);
    }

    /**
     * Creates a new Shortcode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
/*    public function actionCreate()
    {
        $model = new Shortcode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing Shortcode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Shortcode::findModel($id);

        $this->pageName = Yii::t('plugins/default', 'Update {modelClass}: ', [
                'modelClass' => 'Shortcode',
            ]) . ' ' . $model->tag;
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'Shortcodes'), 'url' => ['index']];
        $this->breadcrumbs[] = Yii::t('app/default', 'UPDATE');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Shortcode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Shortcode::findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
