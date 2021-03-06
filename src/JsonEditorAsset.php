<?php

namespace panix\mod\plugins;

use yii\web\AssetBundle;

class JsonEditorAsset extends AssetBundle
{
    public $sourcePath = '@bower/jsoneditor';
    public $js = [
        'jsoneditor.min.js'
    ];
    public $css = [
        'jsoneditor.min.css'
    ];
}
