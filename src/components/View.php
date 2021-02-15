<?php

namespace panix\mod\plugins\components;


use Yii;
use yii\helpers\Url;
use yii\web\View as WebView;
use panix\mod\seo\models\SeoUrl;
use panix\engine\CMS;
use panix\engine\Html;

/**
 * Class View
 * @property string $description
 * @property string $canonical
 * @property string $h1
 * @property string $text
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
    public $description;
    public $canonical;
    private $seo_config;
    protected $data;
    private $cacheModel;
    protected $_model;
    public $seo = null;
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

//835652742:AAES6NfEgJm7GMWmKzxkOy861ppAHkCezZo
    private function seoName($url)
    {
        if ($url->title) {
            if ($url->params) {
                foreach ($url->params as $paramData) {
                    $param = $this->getSeoparam($paramData);
                    if ($param) {
                        $url->title = str_replace('{' . $param['tpl'] . '}', $param['item'], $url->title);
                    }
                }
            }
            $this->title = $url->title;
        }

        $this->printMeta('title', $this->title);
        if ($url->description) {
            if ($url->params) {
                foreach ($url->params as $paramData) {
                    $param = $this->getSeoparam($paramData);
                    if ($param) {
                        $this->description = str_replace($param['tpl'], $param['item'], $url->description);
                    }
                }
            }
            $this->description = $url->description;
        }
        if ($this->description)
            $this->printMeta('description', $this->description);
    }

    private function printMeta($name, $content)
    {
        $site_name = Yii::$app->settings->get('app', 'sitename');
        $content = strip_tags($content);
        if ($name == "title") {
            if ($this->title) {
                $content .= ' ' . Yii::$app->settings->get('seo', 'title_prefix') . ' ' . $site_name;
            } else {
                $content = $site_name;
            }
            echo "<title>{$content}</title>\n";
        } else {
            $this->registerMetaTag(['name' => $name, 'content' => $content]);
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
            /** @var \yii\db\ActiveRecord $item */
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

    /**
     * @param $model
     * @return array|null|\yii\db\ActiveRecord
     */
    public function seo($model)
    {
        if (isset($this->theme->name)) {
            if ($this->theme->name == 'dashboard') {
                return null;
            }
        }
        if (!$this->cacheModel) {
            if ($model) {
                $this->cacheModel = SeoUrl::find()->where(['owner_id' => $model->primaryKey, 'handler_hash' => $model->getHash()])->one();
                if ($this->cacheModel !== null) {
                    return $this->cacheModel;
                }
            } else {
                $urls = $this->getUrls();
                foreach ($urls as $url) {
                    $this->cacheModel = SeoUrl::find()->where(['url' => $url])->one();
                    if ($this->cacheModel !== null) {
                        return $this->cacheModel;
                    }
                }
            }

        }

        return $this->cacheModel;
    }

    private function getUrls()
    {
        $result = null;
        $urls = Yii::$app->request->url;

        if ($this->seo_config->nested_url) {

            if (isset(Yii::$app->languageManager->default->code) && Yii::$app->languageManager->default->code != Yii::$app->language) {
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
        } else {
            $result[] = $urls;
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

    public function beginPage()
    {
        parent::beginPage();
    }

    /**
     * Content manipulation. Need for correct replacement shortcodes
     */
    public function doBody()
    {
        if (!preg_match("/admin/", Yii::$app->request->getUrl())) {
            if ($this->hasEventHandlers(self::EVENT_DO_BODY)) {
                $event = new ViewEvent([
                    'content' => $this->_body,
                ]);
                $this->trigger(self::EVENT_DO_BODY, $event);
                $this->_body = $event->content;
            }
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

        if (isset($this->theme->name)) {
            if ($this->theme->name != 'dashboard') {
                /*$this->data = $this->getData();
                if ($this->data) {
                    if ($this->data->h1)
                        $this->h1 = $this->data->h1;
                    if ($this->data->text)
                        $this->text = $this->data->text;
                }*/


            }
        }
    }

    public function beforeRender2($viewFile, $params)
    {
        if ($this->_model) {
            if (!$this->seo) {
                $this->seo = $this->seo($this->_model);
            }
            //
            // $seo=false;
            if ($this->seo) {
                if ($this->seo->h1)
                    $this->h1 = $this->seo->h1;
                if ($this->seo->text)
                    $this->text = $this->seo->text;
            }
        }
        return parent::beforeRender($viewFile, $params);
    }

    /**
     * @inheritdoc
     */
    public function beginBody()
    {
        if (isset(Yii::$app->controller->dashboard) && !Yii::$app->controller->dashboard && isset($this->seo_config->google_tag_manager) && !empty($this->seo_config->google_tag_manager)) {

            $this->registerJs(CMS::textReplace($this->seo_config->google_tag_manager_js, ['{code}' => $this->seo_config->google_tag_manager]) . PHP_EOL, self::POS_HEAD, 'google_tag_manager');

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
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $this->registerMetaTag(['charset' => Yii::$app->charset]);
            $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
            $this->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name . ' ' . Yii::$app->version]);

            //<!-- Chrome, Firefox OS and Opera -->
            $this->registerMetaTag(['name' => 'theme-color', 'content' => $this->theme->get('theme_color')]);

            //<!-- Windows Phone -->
            $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => $this->theme->get('theme_color')]);
            $this->registerMetaTag(['name' => 'msapplication-navbutton-color', 'content' => $this->theme->get('theme_color')]);

            //<!-- iOS Safari -->
            $this->registerMetaTag(['name' => 'apple-mobile-web-app-status-bar-style', 'content' => $this->theme->get('theme_color')]);

            if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {

                if (isset($this->seo_config->googleanalytics_id) && !empty($this->seo_config->googleanalytics_id) && isset($this->seo_config->googleanalytics_js)) {
                    $this->registerJsFile('https://www.googletagmanager.com/gtag/js?id=' . $this->seo_config->googleanalytics_id, ['async' => 'async', 'position' => self::POS_HEAD], 'dsa');
                    $this->registerJs(CMS::textReplace($this->seo_config->googleanalytics_js, ['{code}' => $this->seo_config->googleanalytics_id]) . PHP_EOL, self::POS_HEAD, 'googleanalytics');
                }

                $faviconPath = Yii::getAlias('@uploads') . DIRECTORY_SEPARATOR . Yii::$app->settings->get('app', 'favicon');
                $faviconInfo = pathinfo($faviconPath);

                if (file_exists($faviconPath)) {
                    if (isset($faviconInfo['extension'])) {
                        if ($faviconInfo['extension'] == 'ico') {
                            $this->registerLinkTag([
                                'rel' => 'shortcut icon',
                                'type' => "image/x-icon",
                                'href' => '/favicon.ico'
                            ]);
                        } else {
                            $this->registerLinkTag([
                                'rel' => 'icon',
                                'type' => "image/png",
                                'href' => Url::to(['/site/favicon', 'size' => 16])
                            ]);

                            if (isset($this->seo_config->favicon_size) && !empty($this->seo_config->favicon_size)) {
                                $list = explode(',', $this->seo_config->favicon_size);
                                foreach ($list as $size) {
                                    if ($size == 144) {
                                        $this->registerMetaTag(['name' => 'msapplication-TileImage', 'content' => Url::to(['/site/favicon', 'size' => $size])]);
                                    }
                                    $this->registerLinkTag([
                                        'rel' => 'apple-touch-icon',
                                        'sizes' => "{$size}x{$size}",
                                        'href' => Url::to(['/site/favicon', 'size' => $size])
                                    ]);
                                }

                            }
                        }
                    }
                }


                if (isset($this->seo_config->yandex_verification) && !empty($this->seo_config->yandex_verification)) {
                    $this->registerMetaTag(['name' => 'yandex-verification', 'content' => $this->seo_config->yandex_verification]);
                }
                if (isset($this->seo_config->google_site_verification) && !empty($this->seo_config->google_site_verification)) {
                    $this->registerMetaTag(['name' => 'google-site-verification', 'content' => $this->seo_config->google_site_verification]);
                }

                if ($this->canonical) {
                    $this->registerLinkTag(['rel' => 'canonical', 'href' => $this->canonical]);
                } else {
                    if ((isset($this->seo_config->canonical) && $this->seo_config->canonical)) {
                        $canonical = Yii::$app->request->getHostInfo() . '/' . Yii::$app->request->getPathInfo();
                        $this->registerLinkTag(['rel' => 'canonical', 'href' => $canonical]);
                    }
                }


                $this->seo = $this->seo($this->_model);
                // $seo=false;
                if ($this->seo) {

                    $this->seoName($this->seo);
                } else {
                    //if ($this->data) {
                    //    $this->seoName($this->data);
                    // } else {
                    if ($this->description) {
                        $this->printMeta('description', Html::encode($this->description));
                    }

                    $this->printMeta('title', Html::encode($this->title));
                    // }
                }
                $this->registerMetaTag(['property' => 'og:locale', 'content' => Yii::$app->language]);
                $this->registerMetaTag(['property' => 'og:type', 'content' => 'article']);

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
        } else {
            //Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
            //Yii::$app->assetManager->bundles['yii\bootstrap4\BootstrapPluginAsset'] = false;
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

        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $this->copyright(), $content);

        if (Yii::$app->id != 'dashboard') {
            if (!Yii::$app->request->isAjax && !preg_match("#" . base64_decode('e2NvcHlyaWdodH0=') . "#", $content)) { // && !preg_match("/print/", $this->layout)
                // die(\Yii::t('app/default', 'NO_COPYRIGHT'));

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

    public function copyright()
    {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $this->registerCss('
                .pixelion{display:inline-block;position:relative}
                .pixelion span{display:inline-block;position:relative;padding-right:20px}
                .pixelion span:after{display:inline-block;position:absolute;content:"\2014";margin-left:5px}
                .pixelion .pixelion-logo{display:inline-block;font-size:17px;padding: 0 0 0 45px;position:relative;font-family:Pixelion,Montserrat;font-weight:normal;line-height: 40px;}
                .pixelion .pixelion-logo:after{font-weight:normal;content:"\f002";left:0;top:0;position:absolute;font-size:37px;font-family:Pixelion;}
                ', [], 'pixelion');
        }
        return '<a href="//pixelion.com.ua/" class="pixelion" target="_blank"><span>' . Yii::t('app/default', 'PIXELION') . '</span><span class="pixelion-logo">PIXELION</span></a>';
    }


    public function getModel()
    {
        return $this->_model;
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }
}