<?php

use panix\mod\plugins\helpers\BS;
use panix\mod\plugins\models\App;
use panix\mod\plugins\models\Category;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\EventSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('plugins/default', 'Events');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">
    <?= Html::a(Yii::t('plugins/default', 'Create {modelClass}', [
        'modelClass' => Yii::t('plugins/default', 'Event')
    ]), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    <?= $this->render('@plugins/views/admin/_menu') ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'app_id',
                'label' => Yii::t('plugins/default', 'App'),
                'options' => ['style' => 'width: 25px; align: center;'],
                'value' => function ($model) {
                    return BS::appLabel($model->app_id);
                },
                'filter' => ArrayHelper::map(App::find()->orderBy('name')->all(), 'id', 'name'),
                'format' => "raw"
            ],
            [
                'attribute' => 'category_id',
                'label' => Yii::t('plugins/default', 'Category'),
                'value' => function ($model) {
                    if ($model->category_id) {
                        return BS::badge($model->category->name);
                    }
                    return '';
                },
                'filter' => ArrayHelper::map(Category::find()->orderBy('name')->all(), 'id', 'name'),
                'format' => "raw"
            ],
            [
                'attribute' => 'trigger_class',
                'label' => Yii::t('plugins/default', 'Trigger'),
                'value' => function ($model) {
                    return $model->trigger_class . BS::badge('::') . $model->trigger_event;
                },
                'format' => "raw"
            ],
            [
                'attribute' => 'handler_class',
                'label' => Yii::t('plugins/default', 'Handler'),
                'value' => function ($model) {
                    return $model->handler_class . BS::badge('::') . $model->handler_method;
                },
                'format' => "raw"
            ],
            [
                'attribute' => 'pos',
                'label' => Yii::t('plugins/default', 'Pos.')
            ],
            [
                'attribute' => 'status',
                'options' => ['style' => 'width: 75px; align: center;'],
                'value' => function ($model) {
                    return $model->status == $model::STATUS_ACTIVE ? BS::badge('Enabled', BS::TYPE_SUCCESS) : BS::badge('Disabled', BS::TYPE_DANGER);
                },
                'filter' => [
                    1 => Yii::t('plugins/default', 'Enabled'),
                    0 => Yii::t('plugins/default', 'Disabled')
                ],
                'format' => "raw"
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 75px;'],
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a('edit', $url, [
                            'class' => 'btn btn-xs btn-primary',
                            'title' => Yii::t('plugins/default', 'Update'),
                        ]);
                    },
                    'delete' => function ($url) {
                        return Html::a('delete', $url, [
                            'class' => 'btn btn-xs btn-danger',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('plugins/default', 'Are you sure to delete this item?'),
                            'title' => Yii::t('plugins/default', 'Delete'),
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
