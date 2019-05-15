<?php

namespace panix\mod\plugins\components;

use panix\mod\plugins\core\ShortcodeHandler;
use panix\mod\plugins\repositories\EventDbRepository;
use panix\mod\plugins\services\ShortcodeService;
use panix\mod\plugins\shortcodes\ShortcodeParser;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class PluginManager
 * @package panix\mod\plugins\components
 */
class PluginsManager extends Component implements BootstrapInterface
{
    /**
     * Application id for category plugins.
     * Support constants: APP_FRONTEND, APP_BACKEND, APP_COMMON
     * @var integer $appId
     */
    public $appId;

    /**
     * Attaches events to all app models.
     * @var bool
     */
    public $enablePlugins = true;

    /**
     * Shortcodes plugin
     * @var bool
     */
    public $shortcodesParse = true;

    /**
     * Ignore blocks from parsing.
     * Set as array regex ['openTag' => 'closeTag']
     * ```
     *  [
     *      '<pre[^>]*>' => '<\/pre>',
     *      '<style[^>]*>' => '<\/style>',
     *      '<script[^>]*>' => '<\/script>',
     *  ]
     * ```
     * @var null|array
     */
    public $shortcodesIgnoreBlocks = null;


    /**
     * @param \yii\base\Application $app
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        if (!preg_match("/admin/", Yii::$app->request->getUrl())) {
            if (!isset($app->plugins)) {
                throw new InvalidConfigException('Component "plugins" must be set');
            }

            if ($this->enablePlugins && $this->appId) {
                $this->registerEvents($this->appId);
            }

            if ($this->shortcodesParse) {
                Yii::$container->setSingleton(ShortcodeParser::class);
                Yii::$container->set(ShortcodeService::class);
                Event::on(View::class, View::EVENT_DO_BODY, [
                    ShortcodeHandler::class, ShortcodeHandler::PARSE_SHORTCODES
                ], $this);
            }
        }
    }

    /**
     * @param $appId
     */
    protected function registerEvents($appId)
    {
        $repository = new EventDbRepository();
        /** @var  \panix\mod\plugins\models\Event [] $events */
        $events = $repository->findEventsByApp($appId);
        if ($events) {
            foreach ($events as $event) {
                $triggerClass = $event->getTriggerClass();
                $triggerEvent = $event->getTriggerEvent();
                $handler = $event->getHandler();
                if (is_array($handler) && is_callable($handler[0])) {
                    $data = isset($handler[1]) ? array_pop($handler) : null;
                    $append = isset($handler[2]) ? array_pop($handler) : null;
                    Event::on($triggerClass, $triggerEvent, $handler[0], $data, $append);
                } else if (is_callable($handler)) {
                    Event::on($triggerClass, $triggerEvent, $handler);
                }
            }
        }
    }
}