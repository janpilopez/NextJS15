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
    <h1><?='Registrar Consulta Médica' ?></h1>
     <?= $this->render('_form2', [
        'model' => $model,
        'fotos' => null,
        'empleado'=> $empleado,
        'ficha_medica' => $ficha_medica
    ]) ?>
</div>

