<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolMov */

$this->title = 'Create Sys Rrhh Empleados Rol Mov';
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Empleados Rol Movs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-rol-mov-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
