<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpresaServiciosEmpleados*/

$this->title = $model->id_sys_rrhh_cedula;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Empresa Servicios Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empresa-servicios-empleados-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_cedula',
            'id_sys_empresa',
            'id_sys_rrhh_empresa_servicios',
            'nombres',
            'genero',
            'ocupacion',
            'estado',
            'cargas_familiares',
        ],
    ]) ?>

</div>
