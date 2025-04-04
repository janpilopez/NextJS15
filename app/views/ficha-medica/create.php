<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaMedica */

$this->title = 'Registrar Ficha Médica';
$this->params['breadcrumbs'][] = ['label' => 'Ficha Medica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-ficha-medica-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'empleado'=> $empleado,
        'fotos'=> $fotos,
        'contrato' => $contrato,
        'nucleofamiliar' => $nucleofamiliar
    ]) ?>

</div>
