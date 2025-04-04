<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Registar Uniforme Empleados';
$this->params['breadcrumbs'][] = ['label' => 'Empleados Uniforme', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';

?>
<div class="sys-rrhh-empleados-uniforme-create">
    
    <?= $this->render('_form', [
        'model' => $model,
        'fotos'=> $fotos,
    ]) ?>

</div>
