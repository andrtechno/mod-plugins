<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Plugin */

$this->title = Yii::t('plugins/default', 'Create Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
