<?php

use panix\mod\plugins\helpers\BS;
use panix\mod\plugins\models\App;
use yii\helpers\ArrayHelper;
use panix\engine\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\ShortcodeSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">
<div class="shortcode-index">

    <?= $this->render('@plugins/views/admin/_menu') ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'app_id',
                'label' => Yii::t('plugins/default', 'App'),
                'contentOptions' => ['class' => 'text-center'],
                'options' => ['style' => 'width: 25px; align: center;'],
                'value' => function ($model) {
                    return BS::appLabel($model->app_id);
                },
                'filter' => ArrayHelper::map(App::find()->orderBy('name')->all(), 'id', 'name'),
                'format' => "raw"
            ],
            'tag',
            'tooltip',
            [
                'attribute' => 'data',
                'value' => function ($model) {
                    return StringHelper::truncate($model->data, 60);
                },
            ],
            [
                'attribute' => 'status',
                'options' => ['style' => 'width: 75px; align: center;'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model->status == $model::STATUS_ACTIVE ? BS::badge(Yii::t('app/default', 'ON'), BS::TYPE_SUCCESS) : BS::badge(Yii::t('app/default', 'OFF'), BS::TYPE_DANGER);
                },
                'filter' => [
                    1 => Yii::t('app/default', 'ON'),
                    0 => Yii::t('app/default', 'OFF')
                ],
                'format' => "raw"
            ],
            [
                'class' => 'panix\engine\grid\columns\ActionColumn',
                'filter'=>false,
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 75px;'],
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a(Html::icon('edit'), $url, [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'title' => Yii::t('plugins/default', 'Update'),
                        ]);
                    },
                    'delete' => function ($url) {
                        return Html::a(Html::icon('delete'), $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('plugins/default', 'Are you sure to delete this item?'),
                            'title' => Yii::t('app/default', 'DELETE'),
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
</div>
</div>
