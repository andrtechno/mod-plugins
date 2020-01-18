<?php

namespace panix\mod\plugins\controllers\admin;

use panix\mod\plugins\models\search\CategorySearch;
use Yii;
use panix\mod\plugins\models\Category;
use panix\engine\controllers\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends AdminController
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->pageName = Yii::t('plugins/default', 'Categories');
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Category::findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        $this->pageName = Yii::t('plugins/default', 'Create {modelClass}: ', [
                'modelClass' => 'Category',
            ]);
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'Categories'), 'url' => ['index']];
        $this->breadcrumbs[] = Yii::t('app/default', 'CREATE');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Category::findModel($id);
        $this->pageName = Yii::t('plugins/default', 'Update {modelClass}: ', [
                'modelClass' => 'Category',
            ]) . ' ' . $model->name;
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'Categories'), 'url' => ['index']];
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Category::findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
