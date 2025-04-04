<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysRrhhCausaSalidaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-rrhh-causa-salida-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_rrhh_causa_salida') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <?= $form->field($model, 'indemnizacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
