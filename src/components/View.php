<?php

namespace panix\mod\plugins\components;

use panix\engine\CMS;
use Yii;
use yii\helpers\Url;
use yii\web\View as WebView;

/**
 * Class View
 * @package panix\mod\plugins\components
 */
class View extends WebView
{
    private $_body;
    /**
     * @event Event an event that is triggered by [[doBody()]].
     */
    const EVENT_DO_BODY = 'doBody';

    public $h1;
    private $seo_config;


    /**
     * @var string
     */

    private $_from_ajax = false;


    /**
     * @inheritdoc
     */
    public function renderAjax($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        $this->_body = $this->renderFile($viewFile, $params, $context);
        $this->_from_ajax = true;

        ob_start();
        ob_implicit_flush(false);

        $this->beginPage();
        $this->head();
        $this->beginBody();
        $this->doBody();
        echo $this->_body;
        $this->endBody();
        $this->endPage(true);

        return ob_get_clean();
    }

    /**
     * Content manipulation. Need for correct replacement shortcodes
     */
    public function doBody()
    {
        if ($this->hasEventHandlers(self::EVENT_DO_BODY)) {
            $event = new ViewEvent([
                'content' => $this->_body,
            ]);
            $this->trigger(self::EVENT_DO_BODY, $event);
            $this->_body = $event->content;
        }
    }


    /**
     * @inheritdoc
     */
    public function endBody()
    {
        if (!$this->_body) {
            $this->_body = ob_get_clean();
            $this->doBody();
            ob_start();
        }

        $this->trigger(self::EVENT_END_BODY);
        echo self::PH_BODY_END;

        foreach (array_keys($this->assetBundles) as $bundle) {
            $this->registerAssetFiles($bundle);
        }
    }

    public function init()
    {
        $this->seo_config = Yii::$app->settings->get('seo');
        parent::init();

    }

    /**
     * @inheritdoc
     */
    public function beginBody()
    {

        if (isset($this->seo_config->google_tag_manager) && !empty($this->seo_config->google_tag_manager)) {

            $this->registerJs(CMS::textReplace($this->seo_config->google_tag_manager_js, ['{CODE}' => $this->seo_config->google_tag_manager]) . PHP_EOL, self::POS_HEAD, 'google_tag_manager');

            echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $this->seo_config->google_tag_manager . '"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->' . PHP_EOL;
        }
        parent::beginBody();
    }


    /**
     * @inheritdoc
     */
    public function head()
    {

        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPjax) {
            $this->registerMetaTag(['charset' => Yii::$app->charset]);
            $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
            $this->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name . ' ' . Yii::$app->version]);
            $this->registerMetaTag(['name' => 'theme-color', 'content' => 'red']);
            if (isset($this->seo_config->yandex_verification) && !empty($this->seo_config->yandex_verification)) {
                $this->registerMetaTag(['name' => 'yandex-verification', 'content' => $this->seo_config->yandex_verification]);
            }
            if (isset($this->seo_config->google_site_verification) && !empty($this->seo_config->google_site_verification)) {
                $this->registerMetaTag(['name' => 'google-site-verification', 'content' => $this->seo_config->google_site_verification]);
            }

            if (isset($this->seo_config->googleanalytics_id) && !empty($this->seo_config->googleanalytics_id) && isset($this->seo_config->googleanalytics_js)) {
                $this->registerJsFile('https://www.googletagmanager.com/gtag/js?id=' . $this->seo_config->googleanalytics_id, ['async' => 'async', 'position' => self::POS_HEAD], 'dsa');
                $this->registerJs(CMS::textReplace($this->seo_config->googleanalytics_js, ['{CODE}' => $this->seo_config->googleanalytics_id]) . PHP_EOL, self::POS_HEAD, 'googleanalytics');
            }


        } else {
            Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
            Yii::$app->assetManager->bundles['yii\bootstrap4\BootstrapPluginAsset'] = false;
        }

        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {

            if (isset(Yii::$app->seo))
                Yii::$app->seo->run();


            // Open Graph default property
            if (!Yii::$app->request->isAjax || !Yii::$app->request->isPjax) {
                $this->registerMetaTag(['property' => 'og:locale', 'content' => Yii::$app->language]);
                $this->registerMetaTag(['property' => 'og:type', 'content' => 'article']);
            }
            foreach (Yii::$app->languageManager->languages as $lang) {
                if (Yii::$app->language == $lang->code) {
                    $url = Url::to("/" . Yii::$app->request->pathInfo, true);
                } else {
                    $url = Url::to("/{$lang->code}/" . Yii::$app->request->pathInfo, true);
                }


                //$link = ($lang->is_default) ? CMS::currentUrl() : '/' . $lang->code . CMS::currentUrl();


                $this->registerLinkTag(['rel' => 'alternate', 'hreflang' => str_replace('_', '-', $lang->code), 'href' => $url]);
            }
        }

        parent::head();
    }

    /**
     * @inheritdoc
     */
    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);
        $endPage = ob_get_clean();

        if ($this->_from_ajax) {
            $content = $endPage;
        } else {
            $content = $this->_body . $endPage;
        }
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPjax) {
            $this->registerCss('
                #pixelion span.cr-logo{display:inline-block;font-size:17px;padding: 0 0 0 45px;position:relative;font-family:Pixelion,Montserrat;font-weight:normal;line-height: 40px;}
                #pixelion span.cr-logo:after{font-weight:normal;content:"\f002";left:0;top:0;position:absolute;font-size:37px;font-family:Pixelion;}
                ', [], 'pixelion');
        }
        $copyright = '<a href="//pixelion.com.ua/" id="pixelion" target="_blank"><span>' . Yii::t('app', 'PIXELION') . '</span> &mdash; <span class="cr-logo">PIXELION</span></a>';
        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $copyright, $content);


        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {
            if (!Yii::$app->request->isAjax && !preg_match("#" . base64_decode('e2NvcHlyaWdodH0=') . "#", $content)) { // && !preg_match("/print/", $this->layout)
                // die(\Yii::t('app', 'NO_COPYRIGHT'));

                Yii::$app->controllerMap['maintenance'] = [
                    'class' => 'panix\engine\maintenance\controllers\MaintenanceController',
                    'title' => 'asddsa'
                ];

                Yii::$app->catchAll = ['maintenance/index', 'title' => 'zzzzzzzzzzzz'];

            }
        }

        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);

        $this->clear();
    }
}