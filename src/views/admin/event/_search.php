<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model panix\mod\plugins\models\search\EventSearch
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'plugin_id') ?>
    <?= $form->field($model, 'trigger_class') ?>
    <?= $form->field($model, 'trigger_event') ?>
    <?= $form->field($model, 'handler_class') ?>
    <?= $form->field($model, 'handler_method') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('plugins/default', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('plugins/default', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
