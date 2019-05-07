<?php

use panix\mod\plugins\helpers\BS;
use panix\engine\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\CategorySearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */


?>
<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">
        <div class="category-index">
            <?= Html::a(Yii::t('plugins/default', 'Create {modelClass}', [
                'modelClass' => Yii::t('plugins/default', 'Category')
            ]), ['create'], ['class' => 'btn btn-success pull-right']) ?>
            <?= $this->render('@plugins/views/admin/_menu') ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
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
                                    'title' => Yii::t('plugins/default', 'Delete'),
                                ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>