<?php

namespace panix\mod\plugins\controllers\admin;


use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use panix\mod\plugins\Module;
use panix\mod\plugins\services\PluginService;
use panix\mod\plugins\models\Plugin;
use panix\mod\plugins\models\search\PluginSearch;
use panix\engine\controllers\AdminController;

/**
 * Class PluginController
 * @package panix\mod\plugins\controllers
 * @property \panix\mod\plugins\Module $module
 */
class PluginController extends AdminController
{
    private $pluginService;

    public function __construct($id, Module $module, PluginService $pluginService, $config = [])
    {
        $this->pluginService = $pluginService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PluginSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->pageName = Yii::t('plugins/default', 'PLUGINS');
        $this->breadcrumbs[] = $this->pageName;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param null $id
     * @return string
     */
    public function actionInstall($id = null)
    {
       // try {
            $plugins = $this->pluginService->getPlugins($this->module->pluginsDir);


        $this->pageName = Yii::t('plugins/default', 'Install');
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'PLUGINS'), 'url' => ['info']];
        $this->breadcrumbs[] = $this->pageName;


            $dataProvider = new ArrayDataProvider([
                'allModels' => $plugins,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

            if ($id && Yii::$app->request->isPost) {
                $this->pluginService->installPlugin($id);
                return $this->redirect('install');
            }

            return $this->render('install', compact('dataProvider'));

       // } catch (Exception $e) {
         //   print_r($e->getMessage());die;
            $this->pluginService->noty->error($e->getMessage());
          //  return $this->redirect('index');
       // }
    }

    /**
     * Displays a info page
     * @return mixed
     */
    public function actionInfo()
    {
        $this->pageName = Yii::t('plugins/default', 'Info');
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'PLUGINS'), 'url' => ['info']];
        $this->breadcrumbs[] = $this->pageName;

        return $this->render('info');
    }

    /**
     * Displays a single Item model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Plugin::findModel($id),
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Plugin::findModel($id);


        $this->pageName = Yii::t('plugins/default', 'Update {modelClass}: ', [
                'modelClass' => 'Item',
            ]) . ' ' . $model->name;
        $this->breadcrumbs[] = ['label' => Yii::t('plugins/default', 'PLUGINS'), 'url' => ['index']];
        $this->breadcrumbs[] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
        $this->breadcrumbs[] = Yii::t('plugins/default', 'Update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionDelete($id)
    {
        $model = Plugin::findModel($id);

        if ($model->id != $model::EVENTS_CORE) {
            $model->delete();
        } else {
            throw new Exception('Core plugin not deleted');
        }

        return $this->redirect(['index']);
    }

}
