<?php

namespace panix\mod\plugins\components;

use panix\engine\CMS;
use panix\engine\Html;
use Yii;
use yii\helpers\Url;
use yii\web\View as WebView;
use panix\mod\seo\models\SeoUrl;


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
    public $text;
    private $seo_config;
    protected $data;

    /**
     * @var string
     */

    private $_from_ajax = false;

    protected function getData()
    {
        $urls = $this->getUrls();
        foreach ($urls as $url) {
            $urlF = SeoUrl::find()->where(['url' => $url])->one();
            if ($urlF !== null) {
                return $urlF;
            }
        }
    }

    private function seoName($url)
    {
        $controller = Yii::$app->controller;

        if ($url->title) {
            if (isset($url->params)) {
                foreach ($url->params as $paramData) {
                    $param = $this->getSeoparam($paramData);
                    if ($param) {

                        $url->title = str_replace('{' . $param['tpl'] . '}', $param['item'], $url->title);
                    }
                }
            }
            $this->title .= $url->title;
            //$this->printMeta('title', Yii::$app->view->title);
        } else {
            // if (!Yii::$app->view->title) {
            //     Yii::$app->view->title = Yii::$app->settings->get('app', 'site_name');
            // }
        }
        $this->printMeta('title', $this->title);
        if ($url->description) {
            if (isset($url->params)) {
                foreach ($url->params as $paramData) {
                    $param = $this->getSeoparam($paramData);
                    if ($param) {
                        $url->description = str_replace($param['tpl'], $param['item'], $url->description);
                    }
                }
            }
            $this->printMeta('description', $url->description);
        } else {
            if (isset($controller->description))
                $this->printMeta('description', $controller->description);
        }
    }

    private function printMeta($name, $content)
    {

        $content = strip_tags($content);
        if ($name == "title") {
            echo "<title>{$content}</title>\n";
        } else {
            $this->registerMetaTag(['name' => $name, 'content' => $content]);
            // echo "<meta name=\"{$name}\" content=\"{$content}\" />\n";
        }
    }

    private function getSeoparam($pdata)
    {

        $urls = Yii::$app->request->url;

        $data = explode("/", $urls);
        $id = $data[count($data) - 1];
        /* если есть символ ">>" значит параметр по связи */

        // $param = $pdata->obj;
        $tpl = $pdata->param;
        if (strstr($tpl, ".")) {
            $paramType = true;
            $data = explode(".", $tpl);
            $tpl2 = explode("/", $data[0]);
        } else {
            $paramType = false;
            $tpl2 = explode("/", $tpl);
        }

        if (class_exists($pdata->modelClass, false)) {
            /** @var $item yii/db/ActiveRecord */
            $item = new $pdata->modelClass;
            if (is_string($id)) {
                $item = $item->find()->where(['slug' => $id])->one();
            } else {
                $item = $item->one($id);
            }

            //echo $item['slug'];die;
            if (count($item)) {
                // var_dump($pdata->param);
                // var_dump($pdata->obj);die;
                // if($pdata->obj){

                return [
                    'tpl' => $tpl,
                    'item' => ($paramType) ? $item[$tpl2[1]][$data[1]] : $item[$tpl2[0]],
                ];
                // }
            }
        } else {

            return false;
        }
    }

    private function getUrls()
    {
        $result = null;
        $urls = Yii::$app->request->url;
        if (Yii::$app->languageManager->default->code != Yii::$app->language) {
            $urls = str_replace('/' . Yii::$app->language, '', $urls);
        }

        $data = explode("/", $urls);
        $count = count($data);

        while (count($data)) {
            $_url = "";
            $i = 0;
            foreach ($data as $key => $d) {
                $_url .= $i++ ? "/" . $d : $d;
            }
            if ($count == 1) {
                $result[] = $_url;
                $result[] = $_url . "/*";
            } else {
                $result[] = $_url . "/*";
                $result[] = $_url;
            }

            unset($data[$key]);
        }
        //$result[] = "/*";
        //$result[] = "/";
        $result22 = array_unique($result);

        return $result22;
    }


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

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->seo_config = Yii::$app->settings->get('seo');
        parent::init();

        if ($this->theme->name != 'dashboard') {
            $this->data = $this->getData();
            if ($this->data) {
                if ($this->data->h1)
                    $this->h1 = $this->data->h1;
                if ($this->data->text)
                    $this->text = $this->data->text;
            }
        }
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


        } else {
            Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
            Yii::$app->assetManager->bundles['yii\bootstrap4\BootstrapPluginAsset'] = false;
        }

        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {


            if (isset($this->seo_config->yandex_verification) && !empty($this->seo_config->yandex_verification)) {
                $this->registerMetaTag(['name' => 'yandex-verification', 'content' => $this->seo_config->yandex_verification]);
            }
            if (isset($this->seo_config->google_site_verification) && !empty($this->seo_config->google_site_verification)) {
                $this->registerMetaTag(['name' => 'google-site-verification', 'content' => $this->seo_config->google_site_verification]);
            }
            if (isset($this->seo_config->canonical) && $this->seo_config->canonical) {
                $canonical = Yii::$app->request->getHostInfo() . '/' . Yii::$app->request->getPathInfo();
                $this->registerLinkTag(['rel' => 'canonical', 'href' => $canonical]);
            }
            if (isset($this->seo_config->googleanalytics_id) && !empty($this->seo_config->googleanalytics_id) && isset($this->seo_config->googleanalytics_js)) {
                $this->registerJsFile('https://www.googletagmanager.com/gtag/js?id=' . $this->seo_config->googleanalytics_id, ['async' => 'async', 'position' => self::POS_HEAD], 'dsa');
                $this->registerJs(CMS::textReplace($this->seo_config->googleanalytics_js, ['{CODE}' => $this->seo_config->googleanalytics_id]) . PHP_EOL, self::POS_HEAD, 'googleanalytics');
            }

            if ($this->data) {
                $this->seoName($this->data);
                $titleFlag = false;
            } else {
                $titleFlag = true;
            }

            if ($this->title) {
             //   $this->title .= ' ' . $this->seo_config->title_prefix . ' ' . Yii::$app->settings->get('app', 'sitename');
            } else {
             //   $this->title .= Yii::$app->settings->get('app', 'sitename');
            }

            if ($titleFlag) {
              //  $this->printMeta('title', Html::encode($this->title));
            }


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


        if (Yii::$app->id != 'dashboard') {
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