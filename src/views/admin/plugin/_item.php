<?php
use panix\mod\plugins\helpers\BS;
use yii\helpers\Html;

/**
 * @var \panix\mod\plugins\dto\PluginDataDto $model
 * @var array $key
 */

if ($model->isInstalled()) {
    $name = 'Update';
    $ver = BS::badge($model->version) . ' to ' . BS::badge($model->new_version, BS::TYPE_SUCCESS);
    $class = BS::TYPE_SUCCESS;
} else {
    $name = 'Install';
    $ver = BS::badge($model->new_version, BS::TYPE_PRIMARY);
    $class = BS::TYPE_PRIMARY;
};

echo Html::beginTag('tr');
echo Html::tag('td', $model->name);
echo Html::tag('td', $ver, ['class' => 'text-center']);
echo Html::tag('td', $model->author, ['class' => 'text-center']);
echo Html::tag('td', $model->text);

echo Html::tag('td', Html::a($name, ['install', 'id' => $key], [
    'class' => 'btn btn-sm btn-' . $class,
    'data' => [
        'method' => 'post'
    ]
]));
echo Html::endTag('tr');
