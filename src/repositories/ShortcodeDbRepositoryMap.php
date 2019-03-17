<?php

namespace panix\mod\plugins\repositories;
use panix\mod\plugins\helpers\JsonHelper;
use panix\mod\plugins\models\Shortcode;

/**
 * Class ShortcodeDbRepositoryMap
 * @package panix\mod\plugins\repositories
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ShortcodeDbRepositoryMap
{
    public $id;
    public $app_id;
    public $handler_class;
    public $tag;
    public $tooltip;
    public $data;
    public $text;
    public $status = Shortcode::STATUS_ACTIVE;

    /**
     * ShortcodeDbRepositoryMap constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        if ($this->data) {
            $this->data = JsonHelper::encode($this->data);
        }
    }
}
