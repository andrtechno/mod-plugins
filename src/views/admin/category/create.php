<?php


/* @var $this yii\web\View */
/* @var $model panix\mod\plugins\models\Category */

$this->title = Yii::t('plugins/default', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugins/default', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
