<?php ///[yii2-theme]

/**
 * Yii2 theme
 *
 * @link        http://www.brainbook.cc
 * @see         https://github.com/yongtiger/yii2-theme
 * @author      Tiger Yong <tigeryang.brainbook@outlook.com>
 * @copyright   Copyright (c) 2017 BrainBook.CC
 * @license     http://opensource.org/licenses/MIT
 */

namespace yongtiger\theme;

use Yii;

/**
 * Theme asset bundle.
 *
 * You can extends `\yongtiger\theme\AssetBundle` in your own theme yii2 extensions, such as `themeyii`.
 * @see https://github.com/yongtiger/yii2-theme-yii
 *
 * ```php
 * class AppAsset extends \yongtiger\theme\AssetBundle
 * {
 *     // static $themePath = '@yongtiger/themeyii2'; ///optional
 *     // static $themeUrlReplace = '{themeurl}';    ///optional
 *     public $css = [
 *         'css/site.css',
 *     ];
 *     public $js = [
 *     ];
 *     public $depends = [
 *         'yii\web\YiiAsset',
 *         'yii\bootstrap\BootstrapAsset',
 *     ];
 * }
 *
 * class OtherThemeAsset extends \yongtiger\theme\AssetBundle
 * {
 *     public $css = [
 *         // 'css/other.css',
 *     ];
 * 
 *     public $js = [
 *         // 'js/other.js',
 *     ];
 *     
 *     public $depends = [
 *         'yongtiger\themeyii\ThemeAsset',    ///note!
 *         // other depends
 *     ];
 * }
 * ```
 *
 * @package yongtiger\theme
 */
class AssetBundle extends \yii\web\AssetBundle
{
    static $themePath;
    static $themeUrlReplace = '{themeurl}';
    static $bundle;
    static $publishedUrl;

    /**
     * Initializes the bundle.
     * If you override this method, make sure you call the parent implementation in the last.
     */
    public function init()
    {
        if ($this->sourcePath === null) {
            $this->sourcePath = static::getThemePath() . '/assets';
        }
        parent::init();
    }
    
    /**
     * Registers this asset bundle with a view.
     *
     * @param View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        if (static::$bundle === null) {
            static::$bundle = parent::register($view);
        }
        return static::$bundle;
    }

    /**
     * Gets the called theme class path.
     *
     * @return string the directory of called theme class.
     */
    public static function getThemePath()
    {
        $reflector = new \ReflectionClass(get_called_class());
        $namespaceName = $reflector->getNamespaceName();
        static::$themePath = '@' . str_replace('\\', '/', $namespaceName);    ///@see \vendor\yiisoft\yii2\base\Module.php(257)

        return static::$themePath;
    }

    /**
     * Gets the published URL for the theme assets path.
     *
     * @return string the published URL for the theme assets path.
     */
    public static function getPublishedUrl()
    {
        return Yii::$app->assetManager->getPublishedUrl(static::getThemePath() . '/assets');
    }

    /**
     * Formats a message.
     *
     * @param string $message the message to be formatted.
     * @return string the formatted message.
     */
    public static function format($message)
    {
        return str_ireplace(static::$themeUrlReplace, static::getPublishedUrl(), $message);
    }

    /**
     * Registers the translation files.
     */
    public static function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations[static::getThemePath() . '/*']) && !isset(Yii::$app->i18n->translations[static::getThemePath()])) {
            Yii::$app->i18n->translations[static::getThemePath() . '/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => static::getThemePath() . '/messages',
                'fileMap' => [
                    static::getThemePath() . '/message' => 'message.php',
                ],
            ];
        }
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t().
     *
     * @see http://www.yiiframework.com/doc-2.0/yii-baseyii.html#t()-detail
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        static::registerTranslations();
        return Yii::t(static::getThemePath() . '/' . $category, $message, $params, $language);
    }
}
