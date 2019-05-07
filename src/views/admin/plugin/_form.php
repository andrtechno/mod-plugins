<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Plugin */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">
        <div class="item-form">


            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'author_url')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'version')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'status')->dropDownList([
                        $model::STATUS_INACTIVE => Yii::t('plugins/default', 'Disabled'),
                        $model::STATUS_ACTIVE => Yii::t('plugins/default', 'Enabled')
                    ]) ?>
                </div>
            </div>



        </div>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>