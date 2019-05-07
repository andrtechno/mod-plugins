<?php

/* 
 * This file is part of the yii2-plugins-system module
 */

use yii\bootstrap4\Nav;

echo Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => [
        [
            'label' => Yii::t('plugins/default', 'PLUGINS'),
            'url' => ['/admin/plugins/plugin/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Events'),
            'url' => ['/admin/plugins/event/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Shortcodes'),
            'url' => ['/admin/plugins/shortcode/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Categories'),
            'url' => ['/admin/plugins/category/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Info'),
            'url' => ['/admin/plugins/plugin/info'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Install'),
            'url' => ['/admin/plugins/plugin/install'],
        ],
    ]
]);
