<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */

$this->title = 'Actualizar Periodo';
$this->params['breadcrumbs'][] = ['label' => 'Periodos', 'url' => ['index']];
?>
<div class="sys-rrhh-empleados-rol-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'mes'=> $mes,
        'anio'=> $anio,
        'periodo'=> $periodo,
        'periodos'=> $periodos
    ]) ?>

</div>
