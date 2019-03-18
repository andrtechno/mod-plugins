<?php

namespace panix\mod\plugins\services;

use panix\mod\plugins\components\FlashNotification;
use panix\mod\plugins\components\Transaction;
use panix\mod\plugins\dto\EventsDiffDto;
use panix\mod\plugins\dto\EventsPoolDto;
use panix\mod\plugins\dto\PluginDataDto;
use panix\mod\plugins\dto\PluginsDiffDto;
use panix\mod\plugins\dto\PluginsPoolDto;
use panix\mod\plugins\dto\ShortcodesDiffDto;
use panix\mod\plugins\dto\ShortcodesPoolDto;
use panix\mod\plugins\repositories\EventDbRepository;
use panix\mod\plugins\repositories\EventDirRepository;
use panix\mod\plugins\repositories\PluginDbRepository;
use panix\mod\plugins\repositories\PluginDirRepository;
use panix\mod\plugins\repositories\ShortcodeDbRepository;
use panix\mod\plugins\repositories\ShortcodeDirRepository;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class PluginService
{
    /**
     * @var FlashNotification
     */
    public $noty;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     *  Repositories
     */
    private $pluginDbRepository;
    private $pluginDirRepository;
    private $eventDbRepository;
    private $eventDirRepository;
    private $shortcodeDbRepository;
    private $shortcodeDirRepository;

    /**
     * Dto
     */
    private $_pluginsPoolDir;
    private $_pluginsPoolDb;
    private $_pluginsDiffDb;
    private $_pluginsDiffDir;


    public function __construct(
        FlashNotification $noty,
        Transaction $transaction,
        PluginDirRepository $pluginDirRepository,
        PluginDbRepository $pluginDbRepository,
        EventDirRepository $eventDirRepository,
        EventDbRepository $eventDbRepository,
        ShortcodeDirRepository $shortcodeDirRepository,
        ShortcodeDbRepository $shortcodeDbRepository
    )
    {
        $this->noty = $noty;
        $this->transaction = $transaction;
        $this->pluginDirRepository = $pluginDirRepository;
        $this->pluginDbRepository = $pluginDbRepository;
        $this->eventDirRepository = $eventDirRepository;
        $this->eventDbRepository = $eventDbRepository;
        $this->shortcodeDirRepository = $shortcodeDirRepository;
        $this->shortcodeDbRepository = $shortcodeDbRepository;
    }


    /**
     * @param $dirs
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getPlugins($dirs)
    {

        $this->pluginDirRepository->setDirs($dirs);




        $pluginsDiffDir = $this->getPluginsDiffDir();
        $pluginsDiffDb = $this->getPluginsDiffDb();

        $pluginsPoolDir = $this->getPluginsPoolDir();
        $pluginsPoolDb = $this->getPluginsPoolDb();

        $data = [];
        foreach (array_filter(array_diff($pluginsDiffDir->getDiff(), $pluginsDiffDb->getDiff())) as $key => $value) {
            $pool = ArrayHelper::merge($pluginsPoolDb->getInfo($key), $pluginsPoolDir->getInfo($key));
            $data[$key] = new PluginDataDto($pool);
        }
        return $data;
    }

    /**
     * @param $hash
     * @throws Exception
     */
    public function installPlugin($hash)
    {
        $pluginsPoolDir = $this->getPluginsPoolDir();
        $pluginInfoDir = $pluginsPoolDir->getInfo($hash);

        if (!$pluginInfoDir) {
            throw new Exception("Can't install plugin");
        }

        $pluginsPoolDb = $this->getPluginsPoolDb();
        $pluginInfoDb = $pluginsPoolDb->getInfo($hash);

        $pluginDataDto = new PluginDataDto($pluginInfoDir);

        $pluginClass = $pluginDataDto->getPluginClass();

        $process = $this->transaction->begin();

        try {
            /** install shortcodes */
            if ($pluginDataDto->isShortcodes()) {
                $this->installShortcodes($hash, $pluginClass, $pluginInfoDb, $pluginInfoDir);
            }

            /** install events */
            if ($pluginDataDto->isEvents()) {
                $this->installEvents($hash, $pluginClass, $pluginInfoDb, $pluginInfoDir);
            }
            $this->transaction->commit($process);
        } catch (\Exception $e) {
            $this->transaction->rollBack($process);
            $this->noty->error($e->getMessage());
        }
    }

    /**
     * @param $hash
     * @param $pluginClass
     * @param $pluginInfoDb
     * @param $pluginInfoDir
     */
    protected function installEvents($hash, $pluginClass, $pluginInfoDb, $pluginInfoDir)
    {
        $eventsArrayDir = $this->eventDirRepository->findEventsByHandler($pluginClass);

        if (!$pluginInfoDb) {
            /** Install plugin */
            $pluginModel = $this->pluginDbRepository->addPlugin($pluginInfoDir);

            foreach ($eventsArrayDir as $data) {
                $eventModel = $this->eventDbRepository->addEvent($data);
                $this->pluginDbRepository->linkEvent($pluginModel, $eventModel);
            }

            $this->noty->success('Plugin installed');

        } else {
            /** Update plugin */
            $data = ArrayHelper::merge($pluginInfoDb, $pluginInfoDir);
            $pluginModel = $this->pluginDbRepository->savePlugin($hash, $data);

            $eventsArrayDb = $this->eventDbRepository->findEventsByHandler($pluginClass);

            $eventsDiffDir = new EventsDiffDto($eventsArrayDir);
            $eventsDiffDb = new EventsDiffDto($eventsArrayDb);

            $eventsPoolDir = new EventsPoolDto($eventsArrayDir);
            $eventsPoolDb = new EventsPoolDto($eventsArrayDb);

            /** Get Deleted events */
            foreach (array_filter(array_diff($eventsDiffDb->getDiff(), $eventsDiffDir->getDiff())) as $key => $value) {
                $data = $eventsPoolDb->getInfo($key);
                $this->eventDbRepository->deleteEvent($data);
            }

            /** Get Installed events */
            foreach (array_filter(array_diff($eventsDiffDir->getDiff(), $eventsDiffDb->getDiff())) as $key => $value) {
                $data = $eventsPoolDir->getInfo($key);
                $eventModel = $this->eventDbRepository->addEvent($data);
                $this->pluginDbRepository->linkEvent($pluginModel, $eventModel);
            }

            $this->noty->success('Plugin updated');
        }
    }

    /**
     * @param $hash
     * @param $pluginClass
     * @param $pluginInfoDb
     * @param $pluginInfoDir
     */
    protected function installShortcodes($hash, $pluginClass, $pluginInfoDb, $pluginInfoDir)
    {
        $shortcodesArrayDir = $this->shortcodeDirRepository->findShortcodesByHandler($pluginClass);

        if (!$pluginInfoDb) {
            /** Install plugin */
            $pluginModel = $this->pluginDbRepository->addPlugin($pluginInfoDir);

            foreach ($shortcodesArrayDir as $data) {
                $shortcodeModel = $this->shortcodeDbRepository->addShortcode($data);
                $this->pluginDbRepository->linkShortcode($pluginModel, $shortcodeModel);
            }

            $this->noty->success('Shortcodes installed');

        } else {
            /** Update plugin */
            $data = ArrayHelper::merge($pluginInfoDb, $pluginInfoDir);
            $pluginModel = $this->pluginDbRepository->savePlugin($hash, $data);

            $shortcodesArrayDb = $this->shortcodeDbRepository->findShortcodesByHandler($pluginClass);

            $shortcodesDiffDir = new ShortcodesDiffDto($shortcodesArrayDir);
            $shortcodesDiffDb = new ShortcodesDiffDto($shortcodesArrayDb);

            $shortcodesPoolDir = new ShortcodesPoolDto($shortcodesArrayDir);
            $shortcodesPoolDb = new ShortcodesPoolDto($shortcodesArrayDb);

            /** Get Deleted shortcodes */
            foreach (array_filter(array_diff($shortcodesDiffDb->getDiff(), $shortcodesDiffDir->getDiff())) as $key => $value) {
                $data = $shortcodesPoolDb->getInfo($key);
                $this->shortcodeDbRepository->deleteShortcode($data);
            }

            /** Get Installed shortcodes */
            foreach (array_filter(array_diff($shortcodesDiffDir->getDiff(), $shortcodesDiffDb->getDiff())) as $key => $value) {
                $data = $shortcodesPoolDir->getInfo($key);
                $shortcodeModel = $this->shortcodeDbRepository->addShortcode($data);
                $this->pluginDbRepository->linkShortcode($pluginModel, $shortcodeModel);
            }

            $this->noty->success('Shortcode updated');
        }
    }

    /**
     * @return PluginsPoolDto
     */
    protected function getPluginsPoolDb()
    {
        if (!$this->_pluginsPoolDb) {
            $this->_pluginsPoolDb = new PluginsPoolDto($this->pluginDbRepository->findAllAsArray());
        }
        return $this->_pluginsPoolDb;
    }

    /**
     * @return PluginsPoolDto
     */
    protected function getPluginsPoolDir()
    {
        if (!$this->_pluginsPoolDir) {
            $this->_pluginsPoolDir = new PluginsPoolDto($this->pluginDirRepository->findAllAsArray());
        }
        return $this->_pluginsPoolDir;
    }

    /**
     * @return PluginsDiffDto
     */
    protected function getPluginsDiffDb()
    {
        if (!$this->_pluginsDiffDb) {
            $this->_pluginsDiffDb = new PluginsDiffDto($this->pluginDbRepository->findAllAsArray());
        }
        return $this->_pluginsDiffDb;
    }

    /**
     * @return PluginsDiffDto
     */
    protected function getPluginsDiffDir()
    {
        if (!$this->_pluginsDiffDir) {
            $this->_pluginsDiffDir = new PluginsDiffDto($this->pluginDirRepository->findAllAsArray());
        }
        return $this->_pluginsDiffDir;
    }

}
