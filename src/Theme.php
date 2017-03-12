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
use yii\helpers\FileHelper;

/**
 * Class Theme
 *
 * The theme pathMap can support view files.
 * 
 * ```php
 * 'view' => [
 *     'theme' => [
 *         'class' => 'yongtiger\\theme\\Theme',
 *         'pathMap' => [
 *             '@backend/views' => '@yongtiger/admin/views',
 *             '@yongtiger/admin/views/layouts/footer.php' => '@backend/views/layouts/footer.php',
 *         ],
 *     ],
 * ],
 * ```
 *
 * @package yongtiger\theme
 */
class Theme extends \yii\base\Theme
{
    /**
     * @inheritdoc
     */
    public function applyTo($path)
    {
        $pathMap = $this->pathMap;
        if (empty($pathMap)) {
            if (($basePath = $this->getBasePath()) === null) {
                throw new InvalidConfigException('The "basePath" property must be set.');
            }
            $pathMap = [Yii::$app->getBasePath() => [$basePath]];
        }

        $path = FileHelper::normalizePath($path);

        foreach ($pathMap as $from => $tos) {
            $from = FileHelper::normalizePath(Yii::getAlias($from));
            if (is_dir($from)) {
                $from = $from . DIRECTORY_SEPARATOR;
                if (strpos($path, $from) === 0) {
                    $n = strlen($from);
                    foreach ((array) $tos as $to) {
                        $to = FileHelper::normalizePath(Yii::getAlias($to)) . DIRECTORY_SEPARATOR;
                        $file = $to . substr($path, $n);
                        if (is_file($file)) {
                            return $file;
                        }
                    }
                }  
            } else if (is_file($from) && $path == $from) {
                foreach ((array) $tos as $to) {
                    $to = FileHelper::normalizePath(Yii::getAlias($to));
                    if (is_file($to)) {
                        return $to;
                    }
                }
            }

        }

        return $path;
    }
}