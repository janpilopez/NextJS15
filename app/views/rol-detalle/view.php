<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolMov */

$this->title = $model->anio;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Empleados Rol Movs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-rol-mov-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_rrhh_concepto' => $model->id_sys_rrhh_concepto, 'id_sys_empresa' => $model->id_sys_empresa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_rrhh_concepto' => $model->id_sys_rrhh_concepto, 'id_sys_empresa' => $model->id_sys_empresa], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'anio',
            'mes',
            'periodo',
            'id_sys_rrhh_cedula',
            'id_sys_rrhh_concepto',
            'unidad',
            'cantidad',
            'valor',
            'id_sys_empresa',
            'transaccion_usuario',
            'estado',
            'id_sys_adm_departamento',
        ],
    ]) ?>

</div>
