<?php

namespace panix\mod\plugins;

use yii\helpers\BaseInflector;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class JsonEditor extends InputWidget
{
    public $editorOptions = [
        'mode' => 'tree',
        'modes' => ['code', 'form', 'text', 'tree', 'view'],
    ];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->id;
        } else {
            $this->id = $this->options['id'];
        }

        if (!isset($this->options['style'])) {
            $this->options['style'] = 'height: 250px;';
        }

        if ($this->hasModel()) {
            $this->value = $this->model->{$this->attribute};
            $this->name = Html::getInputName($this->model, $this->attribute);
        }

        if (empty($this->value)) {
            $this->value = '{}';
        }

        $this->options['id'] .= '-jsoneditor';
    }

    public function run()
    {
        $view = $this->getView();
        JsonEditorAsset::register($view);

        $editorName = BaseInflector::camelize($this->id) . 'Editor';

        $view->registerJs(
            "var container = document.getElementById('" . $this->options['id'] . "');
            var options = " . Json::encode($this->editorOptions) . ";
            var json = " . $this->value . ";
            " . $editorName . " = new JSONEditor(container, options, json);
            jQuery('#" . $this->id . "').parents('form').eq(0).submit(function() {
                jQuery('#" . $this->id . "').val(" . $editorName . ".getText());
                return true;
            });"
        );

        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->id]);
        } else {
            echo Html::hiddenInput($this->name, $this->value, ['id' => $this->id]);
        }

        echo Html::tag('div', '', $this->options);
    }
}
