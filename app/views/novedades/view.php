<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosNovedades */

$this->title = $model->id_sys_rrhh_empleados_novedad;
$this->params['breadcrumbs'][] = ['label' => 'Novedades Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-novedades-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_sys_rrhh_empleados_novedad], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_sys_rrhh_empleados_novedad], [
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
          //  'estado',
            'id_sys_rrhh_empleados_novedad',
            'id_sys_rrhh_cedula',
            'id_sys_rrhh_concepto',
            'fecha',
            'cantidad',
            //'id_sys_empresa',
           // 'transaccion_usuario',
            'comentario',
        ],
    ]) ?>

</div>
