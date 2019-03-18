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
            'label' => Yii::t('plugins/default', 'Items'),
            'url' => ['/plugins/plugin/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Events'),
            'url' => ['/plugins/event/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Shortcodes'),
            'url' => ['/plugins/shortcode/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Categories'),
            'url' => ['/plugins/category/index'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Info'),
            'url' => ['/plugins/plugin/info'],
        ],
        [
            'label' => Yii::t('plugins/default', 'Install'),
            'url' => ['/plugins/plugin/install'],
        ],
    ]
]);
