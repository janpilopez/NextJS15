<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search\SysAdmCcostosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-adm-ccostos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_sys_adm_ccosto') ?>

    <?= $form->field($model, 'centro_costo') ?>

    <?= $form->field($model, 'estado') ?>

    <?= $form->field($model, 'id_sys_empresa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
