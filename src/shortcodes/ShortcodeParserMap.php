<?php

namespace panix\mod\plugins\shortcodes;

/**
 * Class ShortcodeParserMap
 * @package panix\mod\plugins\shortcodes
 */
class ShortcodeParserMap
{
    public $tag;
    public $callback;
    public $config;

    /**
     * PluginDataDto constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
