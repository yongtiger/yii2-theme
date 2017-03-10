<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yongtiger\theme\Module;
use yongtiger\theme\AssetBundle;

/* @var $this yii\web\View */
/* @var $model DynamicModel */
/* @var $themes array */

$this->title = Module::t('message', 'Update Theme');

///[uncheck radio button]
///@see http://www.mkyong.com/jquery/how-to-select-a-radio-button-with-jquery/
///@see http://wenda.so.com/q/1364789883063842
$this->registerJs(
<<<JS
    $('input:radio').on('dblclick', function() { 
    	\$(this).attr('checked',false);
    });
JS
);

?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="theme-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'activeIndex')->radioList($themes, 
        ///[v0.2.3 (ADD# radioList itme option)]
        ///@see http://stackoverflow.com/questions/28234684/yii-2-radiolist-template
        [   
            'item' => function($index, $label, $name, $checked, $value) {
                ///[v0.2.4 (ADD# theme screenshot, title)]
                if ($label['screenshot']) {
                    list($publishFile, $publishUrl) = Yii::$app->assetManager->publish($label['path'] . '/' . $label['screenshot']);
                } else {
                    list($publishFile, $publishUrl) = Yii::$app->assetManager->publish('@yongtiger/theme/no-screenshot.png');
                }

                $return = '<div style="display:inline-block">'; ///div horizontal
                $return .= '<img src="' . $publishUrl . '" width="320"><br>';
                $return .= '<label class="radio-inline">';
                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '"></input>';
                $return .= $label['title'] ? : '(no title)';
                $return .= '</label>';
                $return .= '</div>';
                return $return;
            }
        ]
    )->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('message', 'Update'),['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('message', 'Reset'),['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

