<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SysMedFichaMedicaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-med-ficha-medica-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numero') ?>

    <?= $form->field($model, 'id_sys_rrhh_cedula') ?>

    <?= $form->field($model, 'enf_cardiovasculares') ?>

    <?= $form->field($model, 'enf_metabolicos') ?>

    <?php // echo $form->field($model, 'enf_neurologicos') ?>

    <?php // echo $form->field($model, 'enf_oftalmologicos') ?>

    <?php // echo $form->field($model, 'enf_auditivas') ?>

    <?php // echo $form->field($model, 'infecciones_contagiosas') ?>

    <?php // echo $form->field($model, 'enf_veneras') ?>

    <?php // echo $form->field($model, 'traumatismos') ?>

    <?php // echo $form->field($model, 'convulsiones') ?>

    <?php // echo $form->field($model, 'alergias') ?>

    <?php // echo $form->field($model, 'cirugias') ?>

    <?php // echo $form->field($model, 'otras_patologias') ?>

    <?php // echo $form->field($model, 'partos') ?>

    <?php // echo $form->field($model, 'cesarea') ?>

    <?php // echo $form->field($model, 'abortos') ?>

    <?php // echo $form->field($model, 'ultima_menarquia') ?>

    <?php // echo $form->field($model, 'papanicolau') ?>

    <?php // echo $form->field($model, 'mamas') ?>

    <?php // echo $form->field($model, 'ant_familiar_padres') ?>

    <?php // echo $form->field($model, 'ant_familiar_madre') ?>

    <?php // echo $form->field($model, 'ant_familiar_otros') ?>

    <?php // echo $form->field($model, 'exa_craneo') ?>

    <?php // echo $form->field($model, 'exa_ojos') ?>

    <?php // echo $form->field($model, 'exa_cabidad_oral') ?>

    <?php // echo $form->field($model, 'exa_toraz_csps') ?>

    <?php // echo $form->field($model, 'exa_toraz_r1c1') ?>

    <?php // echo $form->field($model, 'exa_abdomen') ?>

    <?php // echo $form->field($model, 'exa_genital') ?>

    <?php // echo $form->field($model, 'exa_extremidades') ?>

    <?php // echo $form->field($model, 'pulso') ?>

    <?php // echo $form->field($model, 'temperatura') ?>

    <?php // echo $form->field($model, 'respiracion') ?>

    <?php // echo $form->field($model, 'pa_max') ?>

    <?php // echo $form->field($model, 'pa_min') ?>

    <?php // echo $form->field($model, 'talla') ?>

    <?php // echo $form->field($model, 'peso') ?>

    <?php // echo $form->field($model, 'exames_laboratorio') ?>

    <?php // echo $form->field($model, 'recomendacion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
