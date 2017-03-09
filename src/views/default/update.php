<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yongtiger\theme\Module;

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

	<?= $form->field($model, 'activeIndex')->inline()->radioList($themes)->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('message', 'Update'),['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('message', 'Reset'),['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

