<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Plugin */

$this->title = Yii::t('plugins/default', 'Update {modelClass}: ', [
    'modelClass' => 'Item',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('plugins/default', 'Update');
?>
<div class="item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
