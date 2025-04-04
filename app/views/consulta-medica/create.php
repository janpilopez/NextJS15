<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedConsultaMedica */

$this->title = 'Registrar Consulta Médica ';
$this->params['breadcrumbs'][] = ['label' => 'Consulta Médica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-consulta-medica-create">
    <h1><?='No.Turno : '.$modelTurno->numero; ?></h1>
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

