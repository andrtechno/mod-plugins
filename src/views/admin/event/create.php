<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Event */

$this->title = Yii::t('plugins/default', 'Create Event');
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Events'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
