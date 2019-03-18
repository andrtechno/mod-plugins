<?php

use panix\mod\plugins\helpers\BS;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\PluginSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('plugins/default', 'Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">
    <?= $this->render('@plugins/views/admin/_menu') ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'url',
                'format' => "raw",
                'options' => ['style' => 'width: 50px; align: center;'],
                'value' => function ($model) {
                    if ($model->url) {
                        return Html::a('link', $model->url, [
                            'class' => 'btn btn-xs btn-' . BS::TYPE_PRIMARY,
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
                'label' => Yii::t('plugins/default', 'Ver.'),
                'options' => ['style' => 'width: 65px; align: center;'],
                'filter' => false,
                'format' => "raw",
                'value' => function ($model) {
                    return BS::badge($model->version);
                }
            ],
            'text:ntext',
            [
                'attribute' => 'status',
                'options' => ['style' => 'width: 75px; align: center;'],
                'value' => function ($model) {
                    return $model->status == $model::STATUS_ACTIVE ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>';
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
                        return Html::a('del', $url, [
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