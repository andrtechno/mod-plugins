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

    public function render22($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    /**
     * @event Event an event that is triggered by [[doBody()]].
     */
    const EVENT_DO_BODY = 'doBody';

    /**
     * @var string
     */
    private $_body;
    private $_from_ajax = false;

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
     * Renders a view in response to an AJAX request.
     *
     * This method is similar to [[render()]] except that it will surround the view being rendered
     * with the calls of [[beginPage()]], [[head()]], [[beginBody()]], [[endBody()]] and [[endPage()]].
     * By doing so, the method is able to inject into the rendering result with JS/CSS scripts and files
     * that are registered with the view.
     *
     * @param string $view the view name. Please refer to [[render()]] on how to specify this parameter.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param object $context the context that the view should use for rendering the view. If null,
     * existing [[context]] will be used.
     * @return string the rendering result
     * @see render()
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
     * Marks the ending of an HTML body section.
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
    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);
        $endPage = ob_get_clean();

        if ($this->_from_ajax) {
            $content = $endPage;
        } else {
            $content = $this->_body . $endPage;
        }
        $this->registerCss('
        #pixelion span.cr-logo{display:inline-block;font-size:17px;padding: 0 0 0 45px;position:relative;font-family:Pixelion,Montserrat;font-weight:normal;line-height: 40px;}
        #pixelion span.cr-logo:after{font-weight:normal;content:"\f002";left:0;top:0;position:absolute;font-size:37px;font-family:Pixelion;}
        ', [], 'pixelion');

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

    /**
     * @inheritdoc
     */
    public function head()
    {
        if (!Yii::$app->request->isAjax) {
            $this->registerMetaTag(['charset' => Yii::$app->charset]);
            $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
            $this->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name . ' ' . Yii::$app->version]);


            $this->registerMetaTag(['name' => 'theme-color', 'content' => 'red']);


        } else {
            Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
            Yii::$app->assetManager->bundles['yii\bootstrap4\BootstrapPluginAsset'] = false;
        }

        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {
            Yii::$app->seo->run();

            // Open Graph default property
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

        parent::head();
    }

}