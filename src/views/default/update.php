<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yongtiger\theme\Module;

/* @var $this yii\web\View */
/* @var $model DynamicModel */
/* @var $themes array */

$this->title = Module::t('message', 'Update Theme');

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

