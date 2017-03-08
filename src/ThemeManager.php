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
use yongtiger\setting\Setting;

/**
 * Class ThemeManager
 *
 * @package yongtiger\theme
 */
class ThemeManager
{
    static $_themes;
	static $_activeTheme;

    /**
     * Gets the themes table `theme/thems`.
     *
     * @return static
     */
    public static function getThemes() {
        if (static::$_themes === null) {
            static::$_themes = Setting::get('theme', 'themes', []);   ///get the themes table `theme/thems` from setting
        }
        return static::$_themes;
    }

    /**
     * Sets the themes table `theme/thems`.
     *
     * @param array $themes the themes table `theme/thems`
     */
    public static function setThemes($themes) {
        Setting::set('theme', 'themes', $themes);
        static::$_themes = $themes;
    }

    /**
     * Gets the active theme.
     *
     * @return array|false the active theme or false if no any active theme exist
     */
    public static function getActiveTheme() {
    	foreach (static::getThemes() as $theme) {
    		if ($theme['active']) {
    			return $theme;
    		}
    	}
    	return false;
    }
}