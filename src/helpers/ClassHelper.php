<?php

namespace panix\mod\plugins\helpers;

use Yii;
use yii\helpers\FileHelper;

/**
 * Class ClassHelper
 * @package panix\mod\plugins\helpers
 */
class ClassHelper
{

    /**
     * @param string|array $dirs
     * @param null|string $callback
     * @return array
     */
    public static function getAllClasses($dirs, $callback = null)
    {
        if (!$dirs) {
            return [];
        }

        if (is_string($dirs)) $dirs = [$dirs];

        $result = [];

        foreach ($dirs as $path) {
            $dir = Yii::getAlias($path);
            $files = FileHelper::findFiles(Yii::getAlias($path), ['only' => ['*.php']]);

            foreach ($files as $filePath) {

                $class = str_replace([$dir, '.php', '/', '@'], [$path, '', '\\', ''], $filePath);

                if ($callback instanceof \Closure) {
                    $className = call_user_func($callback, $class);
                } else {
                    $className = $class;
                }

                if ($className) {
                    $result[] = $className;
                }
            }
        }

        return $result;
    }

    /**
     * @param $className
     * @return null|string
     */
    public static function getPluginInfo($className)
    {
        try {
            if (!$reflection = self::getReflection($className)) {
                return null;
            }
            return $reflection->getDocComment();

        } catch (\Exception $e) {
            echo $className;
            exit;
        }
    }

    /**
     * ```php
     *  ClassHelper::getEventNames(yii\base\View::class);
     * ```
     * @param $className
     * @return array
     */
    public static function getEventNames($className)
    {
        $result = [];
        $reflection = new \ReflectionClass($className);
        foreach ($reflection->getConstants() as $name => $value) {
            if (!preg_match('/^EVENT/', $name)) {
                continue;
            }
            $result[$name] = $value;
        }
        return $result;
    }

    /**
     * @param $className
     * @return bool|\ReflectionClass
     */
    protected static function getReflection($className)
    {
        try {
            if (in_array($className, ['yii\requirements\YiiRequirementChecker', 'yii\helpers\Markdown'])) {
                return false;
            }
            $reflection = new \ReflectionClass($className);
        } catch (\Exception $e) {
            return false;
        }
        return $reflection;
    }
}