<?php

namespace panix\mod\plugins\components;

use Yii;

/**
 * Class FlashNotification
 * @package panix\mod\plugins\components
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class FlashNotification
{
    /**
     * @param $message
     */
    public function success($message)
    {
        Yii::$app->session->setFlash('success', $message);
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        Yii::$app->session->setFlash('error', $message);
    }
}