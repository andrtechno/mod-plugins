<?php

use panix\mod\plugins\helpers\BS;
use panix\engine\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\PluginSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

?>
<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">


        <div class="item-index">
            <?= $this->render('@plugins/views/admin/_menu') ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'url',
                        'format' => "raw",
                        'options' => ['style' => 'width: 50px; align: center;'],
                        'value' => function ($model) {
                            if ($model->url) {
                                return Html::a('link', $model->url, [
                                    'class' => 'btn btn-sm btn-' . BS::TYPE_PRIMARY,
                                    'target' => '_blank'
                                ]);
                            }
                            return '';
                        },
                        'filter' => false
                    ],
                    'name',
                    [
                        'attribute' => 'version',
                        'label' => Yii::t('plugins/default', 'VERSION'),
                        'options' => ['style' => 'width: 65px; align: center;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => false,
                        'format' => "raw",
                        'value' => function ($model) {
                            return BS::badge($model->version);
                        }
                    ],
                    'text:ntext',
                    [
                        'attribute' => 'status',
                        'options' => ['style' => 'width: 75px;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($model) {
                            return $model->status == $model::STATUS_ACTIVE ? '<span class="badge badge-success">' . Yii::t('app/default', 'ON') . '</span>' : '<span class="badge badge-danger">' . Yii::t('app/default', 'OFF') . '</span>';
                        },
                        'filter' => [
                            1 => Yii::t('app/default', 'ON'),
                            0 => Yii::t('app/default', 'OFF')
                        ],
                        'format' => "raw"
                    ],
                    [
                        'class' => 'panix\engine\grid\columns\ActionColumn',
                        'filter' => false,
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