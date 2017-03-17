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
	static $_themeBootstraps;
    
    ///[v0.2.4 (ADD# theme screenshot, title)]
    static $title;
    static $screenshot;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
    	$reflector = new \ReflectionClass(get_called_class());
        $namespaceName = $reflector->getNamespaceName();
    	$themePath = call_user_func([$namespaceName . '\\ThemeAsset', 'getThemePath']);

    	$themes = ThemeManager::getThemes();
    	$themes[] = ['title' => static::$title, 'screenshot' => static::$screenshot, 'namespace' => $namespaceName, 'path' => $themePath, 'active' => false];  ///[v0.2.4 (ADD# theme screenshot, title)]
        ThemeManager::setThemes($themes);

    }

    /**
     * Filters bootstraps in `Yii::$app->extensions`.
     *
     * If the current theme exist in `Yii::$app->extensions`, remove out.
     * In other words, if a theme has been registered in the themes table `theme/thems`, you do not need to perform bootstrap.
     */
    public static function filterExtensionsBootstrap() {
        foreach (ThemeManager::getThemes() as $theme) {

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

        foreach (ThemeManager::getThemes() as $theme) {
            
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
    	$ret = strtr($bootstrapPathPattern, [  ///[v0.3.1 (CHG# \src\Bootstrap.php:public static function formatBootstrapPath)]
            '{theme-path}' => $themePath,
            '{bootstrap-path-file}' => $bootstrapPathFile,
        ]);
    	$path = FileHelper::normalizePath($ret, DIRECTORY_SEPARATOR) . '.php';
    	return [$ret, $path]; 
    }
}