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
 * Class Module
 *
 * @package yongtiger\theme
 */
class Module extends \yii\base\Module
{
    /**
     * @var string module name
     */
    public static $moduleName = 'thememanager';

    ///[v0.4.1 (ADD# getThemesCallback, setThemesCallback)]
    /**
     * @var callable
     */
    public $getThemesCallback;
    /**
     * @var callable
     */
    public $setThemesCallback;

    /**
     * @return static
     */
    public static function instance()
    {
        return Yii::$app->getModule(static::$moduleName);
    }

    /**
     * Registers the translation files.
     */
    public static function registerTranslations()
    {
        ///[i18n]
        ///if no setup the component i18n, use setup in this module.
        if (!isset(Yii::$app->i18n->translations['extensions/yongtiger/yii2-theme/*']) && !isset(Yii::$app->i18n->translations['extensions/yongtiger/yii2-theme'])) {
            Yii::$app->i18n->translations['extensions/yongtiger/yii2-theme/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@vendor/yongtiger/yii2-theme/src/messages',
                'fileMap' => [
                    'extensions/yongtiger/yii2-theme/message' => 'message.php',  ///category in Module::t() is message
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
        return Yii::t('extensions/yongtiger/yii2-theme/' . $category, $message, $params, $language);
    }
}
