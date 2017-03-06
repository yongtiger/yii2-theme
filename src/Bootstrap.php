<?php ///[yii2-theme]

/**
 * Yii2 theme yii
 *
 * @link        http://www.brainbook.cc
 * @see         https://github.com/yongtiger/yii2-theme-yii2
 * @author      Tiger Yong <tigeryang.brainbook@outlook.com>
 * @copyright   Copyright (c) 2017 BrainBook.CC
 * @license     http://opensource.org/licenses/MIT
 */

namespace yongtiger\theme;

use Yii;

use yii\base\BootstrapInterface;
use yii\base\Application;

/**
 * Class Bootstrap
 *
 * @see http://www.yiiframework.com/doc-2.0/guide-structure-extensions.html#bootstrapping-classes
 * @package yongtiger\theme
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () {
             // do something here
        });
    }
}