<?php

use panix\mod\plugins\helpers\BS;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var $this yii\web\View
 * @var $searchModel panix\mod\plugins\models\search\CategorySearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('plugins/default', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">
    <?= Html::a(Yii::t('plugins/default', 'Create {modelClass}', [
        'modelClass' => Yii::t('plugins/default', 'Category')
    ]), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    <?= $this->render('/_menu') ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
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
