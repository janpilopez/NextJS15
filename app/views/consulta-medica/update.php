<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedConsultaMedica */

$this->title = 'Consulta Médica # '.$model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Consulta Medicas', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-consulta-medica-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'empleado'=> $empleado,
        'fotos'=> $fotos,
        'id_categoria_patologia' => $id_categoria_patologia,
        'id_patologia' => $id_patologia,
        'certificados' => $certificados,
        'ficha_medica'=> $ficha_medica,
        'historial_medico' => $historial_medico
    ]) ?>

</div>
