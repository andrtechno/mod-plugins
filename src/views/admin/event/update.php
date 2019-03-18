<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Event */

$this->title = Yii::t('plugins/default', 'Update {modelClass}: ', [
    'modelClass' => 'Event',
]) . ' ' . $model->plugin->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Events'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('plugins/default', 'Update');
?>
<div class="event-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
