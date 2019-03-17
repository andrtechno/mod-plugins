<?php
namespace panix\mod\plugins\shortcodes;

use panix\mod\plugins\helpers\ShortcodesHelper;
use yii\helpers\ArrayHelper;

/**
 * Class Shortcode
 * @package panix\mod\plugins\shorcodes
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class ShortcodeParser
{
    /**
     * Default ignore blocks
     * @var array
     */
    protected $ignoreBlocks = [
        '<!\[CDATA' => '\]\]>',
        '<pre[^>]*>' => '<\/pre>',
        '<form[^>]*>' => '<\/form>',
        '<style[^>]*>' => '<\/style>',
        '<script[^>]*>' => '<\/script>',
        '<!--' => '-->',
        '<code[^>]*>' => '<\/code>',
    ];

    /**
     * Add ignore blocks from array
     * @param array $blocks
     */
    public function addIgnoreBlocks($blocks)
    {
        if (is_array($blocks)) {
            foreach ($blocks as $openTag => $closeTag) {
                $this->addIgnoreBlock($openTag, $closeTag);
            }
        }
    }

    /**
     * Add ignore block
     * @param String $openTag
     * @param String $closeTag
     */
    protected function addIgnoreBlock($openTag, $closeTag)
    {
        $this->ignoreBlocks[$openTag] = $closeTag;
    }

    /**
     * regex for ignore blocks
     * @var string
     */
    private $_ignorePattern;

    /**
     * Associative array of shortcodes and their
     * respective callbacks
     */
    private $_shortcodes = [];

    /**
     * Pool of parsered shortcodes
     * @var array
     */
    private $_pool = [];

    /**
     * @param $tag
     * @return bool
     */
    public function existsShortcode($tag)
    {
        if (isset($this->_shortcodes[$tag])) {
            return true;
        }
        return false;
    }

    /**
     * @param $parser
     * @param array $parser
     */
    public function addShortcode($parser)
    {
        /** @var ShortcodeParserMap */
        $shortcode = new ShortcodeParserMap($parser);

        if ($this->existsShortcode($shortcode->tag)) {
            return;
        } else {
            $this->_shortcodes[$shortcode->tag] = [
                'callback' => $shortcode->callback,
                'config' => $shortcode->config
            ];
        }
    }

    /**
     * @param string $tag
     */
    public function removeShortcode($tag)
    {
        if (array_key_exists($tag, $this->_shortcodes)) {
            unset($this->_shortcodes[$tag]);
        }
    }

    /**
     * Clear all shortcodes.
     *
     * This function is simple, it clears all of the shortcode tags by replacing the
     * shortcodes global by a empty array. This is actually a very efficient method
     * for removing all shortcodes.
     *
     * @since 2.5.0
     */
    public function removeAllShortcodes()
    {
        $this->_shortcodes = [];
    }

    /**
     * @param $tag
     */
    private function addToPool($tag)
    {
        if (!isset($this->_pool[$tag])) {
            $this->_pool[$tag] = $tag;
        }
    }

    /**
     * @return bool
     */
    private function isFinishedParse()
    {
        if (count($this->_pool) == count($this->_shortcodes)) {
            return true;
        }
        return false;
    }

    /**
     * Tests whether content has a particular shortcode
     * @param $content
     * @param $tag
     * @return bool
     */
    public function hasShortcode($content, $tag)
    {
        if (false === strpos($content, '[')) {
            return false;
        }

        if ($this->existsShortcode($tag)) {
            return true;
        }

        preg_match_all($this->shortcodeRegex(), $content, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return false;
        }

        foreach ($matches as $shortcode) {
            if ($tag === $shortcode[2]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse shortcodes in given content
     * @param string $content Content to parse for shortcodes
     * @return string
     */
    public function doShortcode($content)
    {
        if (false === strpos($content, '[')) {
            return $content;
        }

        if (empty($this->_shortcodes) || !is_array($this->_shortcodes))
            return $content;

        /**
         * Clear content from ignore blocks
         */
        $pattern = $this->getIgnorePattern();
        $content = preg_replace_callback("~$pattern~isu", ['self', '_stack'], $content);

        /**
         * parse nested
         */
        $content = $this->parseContent($content);

        /**
         * Replase shorcodes in content
         */
        $content = strtr($content, self::_stack());

        return $content;
    }

    /**
     * parse nested shortcodes
     * @param $content
     * @return string
     */
    protected function parseContent($content)
    {
        $content = preg_replace_callback($this->shortcodeRegex(), [$this, 'doShortcodeTag'], $content);
        if (!$this->isFinishedParse()) {
            $content = $this->parseContent($content);
        }
        return $content;
    }

    /**
     * Calculate ignore blocks as callback.
     * Накапливает исходный код безопасных блоков при использовании в качестве
     * обратного вызова. При отдельном использовании возвращает накопленный
     * массив.
     *
     * @param bool $matches
     * @return array|string
     */
    private static function _stack($matches = false)
    {
        static $safe_blocks = [];
        if ($matches !== false) {
            $key = '<' . count($safe_blocks) . '>';
            $safe_blocks[$key] = $matches[0];
            return $key;
        } else {
            $tmp = $safe_blocks;
            unset($safe_blocks);
            return $tmp;
        }
    }

    /**
     * Parse single shortcode
     * Borrowed from WordPress wp/wp-includes/shortcode.php
     * @param array $m Shortcode matches
     * @return string
     */
    protected function doShortcodeTag($m)
    {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = $this->parseAttributes($m[3]);

        $callback = $this->_shortcodes[$tag]['callback'];
        $config = $this->_shortcodes[$tag]['config'];

        $attr = ArrayHelper::merge($config, $attr);
        $content = isset($m[5]) ? $m[5] : null;

        $attr['content'] = $content;
        $this->addToPool($tag);
        return $m[1] . call_user_func($callback, $attr, $content, $tag) . $m[6];
    }

    /**
     * Remove all shortcode tags from the given content.
     * @uses $shortcode_tags
     * @param string $content Content to remove shortcode tags.
     * @return string Content without shortcode tags.
     */
    public function stripAllShortcodes($content)
    {
        if (empty($this->_shortcodes)) {
            return $content;
        }
        return preg_replace_callback($this->shortcodeRegex(), [$this, 'stripShortcodeTag'], $content);
    }

    /**
     * Strips a tag leaving escaped tags
     * @param $tag
     * @return string
     */
    private function stripShortcodeTag($tag)
    {
        if ($tag[1] == '[' && $tag[6] == ']') {
            return substr($tag[0], 1, -1);
        }
        return $tag[1] . $tag[6];
    }

    /**
     * Get the list of all shortcodes found in given content
     * @param string $content Content to process
     * @return array
     */
    public function getShortcodesFromContent($content)
    {
        $content = $this->getContentWithoutIgnoreBlocks($content);

        if (false === strpos($content, '[')) {
            return [];
        }

        $result = [];

        $regex = "\[([A-Za-z_]+[^\ \]]+)";
        preg_match_all('/' . $regex . '/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $result[$match] = $match;
            }
        }

        if ($result) {
            return array_keys($result);
        }

        return $result;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function getContentWithoutIgnoreBlocks($content)
    {
        $pattern = $this->getIgnorePattern();
        return preg_replace("~$pattern~isu", '', $content);
    }

    /**
     * @return string
     */
    protected function getIgnorePattern()
    {
        if (!$this->_ignorePattern) {
            $pattern = '(';
            foreach ($this->ignoreBlocks as $start => $end) {
                $pattern .= "$start.*?$end|";
            }
            $pattern .= '<.*?>)';
            $this->_ignorePattern = $pattern;
        }
        return $this->_ignorePattern;
    }

    /**
     * Parses attributes from a shortcode
     * @param string $text
     * @return array
     */
    protected function parseAttributes($text)
    {
        return ShortcodesHelper::parseAttributes($text);
    }

    /**
     * @return string The shortcode search regular expression
     */
    protected function shortcodeRegex()
    {
        return ShortcodesHelper::shortcodeRegex($this->_shortcodes);
    }
}

