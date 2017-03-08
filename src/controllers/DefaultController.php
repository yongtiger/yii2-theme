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

namespace yongtiger\theme\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yongtiger\theme\ThemeManager;

/**
 * Default Controller
 *
 * @package yongtiger\theme\controllers
 */
class DefaultController extends Controller
{
    /**
     * Defines the controller behaviors
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //         ],
    //     ];
    // }


    public function actionUpdate()
    {
        $themes = ThemeManager::getThemes();

        ///Model::loadMultiple() fills multiple models with the form data coming from POST and Model::validateMultiple() validates all models at once.
        if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {

            foreach ($settings as $setting) {

                ///[Yii2 setting:multiple-select]Convert the multi-select setting value (not a string, but an array) to JSON string before saving. 
                ///because the array of multi-select values will not contain sub-array (only string or integer), so you can use json_encode converted to JSON string, e.g '["firstname","lastname","age"]'
                ///@see http://www.cnblogs.com/xmphoenix/archive/2011/05/26/2057963.html
                if(in_array($setting['input'], [SettingModel::INPUT_CHECKBOXLIST, SettingModel::INPUT_LISTBOX_MULTIPLE, SettingModel::INPUT_DROPDOWNLIST_MULTIPLE])){
                    $setting->value = json_encode($setting['value']);
                }

                $setting->save(false);  ///passing false as a parameter to save() to not run validation twice
            }

            Yii::$app->session->setFlash('success', Module::t('message', 'Update succeed.'));
            return $this->refresh();
        }

        return $this->render('update', ['categories' => SettingModel::findAllCategories(), 'category' => $category, 'settings' => $settings]);
    }
}
