<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaMedica */

$this->title = 'Actualizar Ficha Médica: # ' . $model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Ficha Medica', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-ficha-medica-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'empleado'=> $empleado,
        'fotos'=> $fotos,
        'contrato' => $contrato,
        'nucleofamiliar' => $nucleofamiliar
    ]) ?>

</div>
