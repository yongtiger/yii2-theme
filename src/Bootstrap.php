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
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yongtiger\setting\Setting;

/**
 * Class Bootstrap
 *
 * @see http://www.yiiframework.com/doc-2.0/guide-structure-extensions.html#bootstrapping-classes
 * @package yongtiger\theme
 */
class Bootstrap implements BootstrapInterface
{
    static $_themes;
	static $_activeTheme;
	static $_themeBootstraps;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
    	$reflector = new \ReflectionClass(get_called_class());
        $namespaceName = $reflector->getNamespaceName();
    	$themePath = call_user_func([$namespaceName . '\\ThemeAsset', 'getThemePath']);

    	$themes = static::getThemes();
    	$themes[] = ['namespace' => $namespaceName, 'path' => $themePath, 'active' => false];
        static::setThemes($themes);

    }

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
     * Filters bootstraps in `Yii::$app->extensions`.
     *
     * If the current theme exist in `Yii::$app->extensions`, remove out.
     * In other words, if a theme has been registered in the themes table `theme/thems`, you do not need to perform bootstrap.
     */
    public static function filterExtensionsBootstrap() {
        foreach (static::getThemes() as $theme) {

            ///Get `Yii::$app->extensions`. @see [[yii2\base\Application]]
            if (Yii::$app->extensions === null) {
                $file = Yii::getAlias('@vendor/yiisoft/extensions.php');
                Yii::$app->extensions = is_file($file) ? include($file) : [];
            }

            ///theme bootstrap class
            $themeBootstrapClass = $theme['namespace'] . '\\Bootstrap'; 
            foreach (Yii::$app->extensions as $key => $extension) {
                ///If the current theme exist in `Yii::$app->extensions`, remove out.
                ///In other words, if a theme has been registered in the setting table `theme/thems`, you do not need to perform bootstrap.
                if (isset($extension['bootstrap']) && $extension['bootstrap'] == $themeBootstrapClass) {
                    unset(Yii::$app->extensions[$key]['bootstrap']);
                }
            }
        }
    }

    /**
     * Filters bootstraps in `themes` folder.
     *
     * If the current theme exist in `Yii::$app->extensions`, remove out.
     * In other words, if a theme has been registered in the themes table `theme/thems`, you do not need to perform bootstrap.
     *
     * @param string $themesRootPath e.g. `themes`
     * @param string $bootstrapPathFile e.g. `src\Bootstrap`
     * @param string $bootstrapPathPattern e.g. `{theme-path}\{bootstrap-path-file}`
     */
    public static function filterThemesBootstrap($themesRootPath = 'themes', $bootstrapPathFile = 'src\\Bootstrap', $bootstrapPathPattern = '{theme-path}\\{bootstrap-path-file}') {

    	if (static::$_themeBootstraps === null) {
    		static::$_themeBootstraps = static::findThemeBootstraps($themesRootPath, $bootstrapPathFile, $bootstrapPathPattern);
    	}

        foreach (static::getThemes() as $theme) {
            
            ///theme bootstrap class
            $themeBootstrapClass = $theme['namespace'] . '\\Bootstrap'; 

            ///If the current theme exist in `Yii::$app->bootstrap`, remove out.
            ///In other words, if a theme has been registered in the setting table `theme/thems`, you do not need to perform bootstrap.
            $key = array_search($themeBootstrapClass, static::$_themeBootstraps);  
            if ($key !== false) {
                array_splice(static::$_themeBootstraps, $key, 1);
            }

        }

        Yii::$app->bootstrap = array_merge(Yii::$app->bootstrap, static::$_themeBootstraps);
    }

    /**
     * Finds all `Bootstrap` class files in the given themes root path.
     *
     * @param string $themesRootPath e.g. `themes`
     * @param string $bootstrapPathFile e.g. `src\Bootstrap`
     * @param string $bootstrapPathPattern e.g. `{theme-path}\{bootstrap-path-file}`
     * @return array e.g. ['themes\yii2themeyii\src\Bootstrap']
     */
    public static function findThemeBootstraps($themesRootPath, $bootstrapPathFile, $bootstrapPathPattern) {

    	$dir = Yii::getAlias('@' . $themesRootPath);
        if (!is_dir($dir)) {
            throw new InvalidParamException("The dir argument must be a directory: $dir");
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        $list = [];
        $handle = opendir($dir);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $dir");
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
            	list($r, $p) = static::formatBootstrapPath($bootstrapPathPattern, $file, $bootstrapPathFile);
                if (is_file($dir . DIRECTORY_SEPARATOR . $p)) {
                    $list[] = $themesRootPath . '\\' . $r;
                    continue;
                }
            }
        }
        closedir($handle);
        return $list;
    }

    /**
     * Formats bootstrap path in the given `$bootstrapPathPattern`, `$themePath` and `$bootstrapPathFile`.
     *
     * @param string $bootstrapPathPattern e.g. `{theme-path}\{bootstrap-path-file}`
     * @param string $themePath e.g. `yii2themeyii` 
     * @param string $bootstrapPathFile e.g. `src\Bootstrap`
     * @return array e.g. `['yii2themeyii\src\Bootstrap', 'yii2themeyii\src\Bootstrap.php']` 
     */
    public static function formatBootstrapPath($bootstrapPathPattern, $themePath, $bootstrapPathFile) {
    	$ret = $bootstrapPathPattern;
    	$ret = str_replace('{theme-path}', $themePath, $ret);
    	$ret = str_replace('{bootstrap-path-file}', $bootstrapPathFile, $ret);
    	$path = FileHelper::normalizePath($ret, DIRECTORY_SEPARATOR) . '.php';
    	return [$ret, $path]; 
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