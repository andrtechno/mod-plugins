<?php

use yii\widgets\LinkPager;
use yii\widgets\ListView;

/**
 * @var yii\web\View $this
 * @var \yii\data\ArrayDataProvider $dataProvider
 */


?>
<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">
        <div class="item-find">

            <?= $this->render('@plugins/views/admin/_menu') ?>
            <?php
            $thead = '<thead>
                <tr>
                    <th>'.Yii::t('plugins/default', 'PLUGIN_NAME').'</th>
                    <th>'.Yii::t('plugins/default', 'VERSION').'</th>
                    <th>'.Yii::t('plugins/default', 'AUTHOR').'</th>
                    <th>'.Yii::t('plugins/default', 'PLUGIN_DESCRIPTION').'</th>
                    <th width="80"></th>
                </tr>
              </thead>';
            ?>

            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "$thead{items}",
                'itemView' => '_item',
                'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered table-striped',
                ],
                'itemOptions' => [
                    'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>

            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
            ]); ?>

        </div>
    </div>
</div>