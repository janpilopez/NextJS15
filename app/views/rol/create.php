<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */

$this->title = 'Registrar Periodos';
$this->params['breadcrumbs'][] = ['label' => 'Periodos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-rol-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'mes'=> $mes,
        'anio'=> $anio,
        'periodos'=> $periodos,
        'periodo'=> $periodo
        
    ]) ?>

</div>
