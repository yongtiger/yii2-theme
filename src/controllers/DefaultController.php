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
use yii\base\Model;
use yii\base\DynamicModel;
use yongtiger\theme\ThemeManager;

/**
 * Default Controller
 *
 * @package yongtiger\theme\controllers
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'update';

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
     * Updates an active theme index.
     * 
     * @return mixed
     */
    public function actionUpdate()
    {
        $activeTheme = ThemeManager::getActiveTheme();
        $model = new DynamicModel(['activeIndex' => $activeTheme === false ? false : key($activeTheme)]);
        $model->addRule('activeIndex', 'safe');

        if ($model->load(Yii::$app->request->post())) {
            
            ///radioList:options:unselect: string, the value that should be submitted when none of the radio buttons is selected. You may set this option to be null to prevent default value submission. If this option is not set, an empty string will be submitted.
            ///@see http://docs.huihoo.com/yii/2.0/yii-helpers-basehtml.html#activeRadioList()-detail
            ThemeManager::setActiveTheme($model->activeIndex === '' ? false : $model->activeIndex);

            // Yii::$app->session->setFlash('success', Module::t('message', 'Update succeed.'));
            return $this->refresh();
        }

        ///[v0.2.3 (ADD# radioList itme option)]
        return $this->render('update', ['model' => $model, 'themes' => ThemeManager::getThemes()]);
    }
}
