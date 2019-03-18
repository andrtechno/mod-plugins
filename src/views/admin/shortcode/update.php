<?php

/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Shortcode */

$this->title = Yii::t('plugins/default', 'Update {modelClass}: ', [
    'modelClass' => 'Shortcode',
]) . ' ' . $model->tag;
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Shortcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('plugins/default', 'Update');
?>
<div class="shortcode-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
