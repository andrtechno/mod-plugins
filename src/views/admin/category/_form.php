<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model panix\mod\plugins\models\Category
 */
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-header">
            <h5><?= $this->context->pageName; ?></h5>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>