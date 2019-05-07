<?php

use panix\mod\plugins\models\App;
use panix\mod\plugins\JsonEditor;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model panix\mod\plugins\models\Shortcode
 * @var $form yii\widgets\ActiveForm
 */
$disabled = true;
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-header">
            <h5><?= $this->context->pageName; ?></h5>
        </div>
        <div class="card-body">


            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'handler_class')->textInput(['disabled' => $disabled, 'maxlength' => true]) ?>
                    <?= $form->field($model, 'tag')->textInput(['disabled' => $disabled, 'maxlength' => true]) ?>
                    <?= $form->field($model, 'tooltip')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'data')->widget(JsonEditor::class,
                        [
                            'editorOptions' => [
                                'modes' => ['code', 'form', 'text', 'tree', 'view'], // available modes
                                'mode' => 'form', // current mode
                            ],
                            'options' => ['style' => 'height:225px'], // html options
                        ]
                    ); ?>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'app_id')->dropDownList(ArrayHelper::map(App::find()->all(), 'id', 'name')) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'status')->dropDownList([
                                $model::STATUS_INACTIVE => Yii::t('plugins/default', 'Disabled'),
                                $model::STATUS_ACTIVE => Yii::t('plugins/default', 'Enabled')
                            ]) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(\panix\mod\plugins\models\Category::find()->orderBy('name')->all(), 'id', 'name'), [
                        'prompt' => ' '
                    ]) ?>
                    <?= $form->field($model, 'text')->textarea() ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>