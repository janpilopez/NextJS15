<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillasJornadasCab */

$this->title = 'Update Sys Rrhh Cuadrillas Jornadas Cab: ' . $model->id_sys_rrhh_cuadrillas_jornadas_cab;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Cuadrillas Jornadas Cabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cuadrillas_jornadas_cab, 'url' => ['view', 'id_sys_rrhh_cuadrillas_jornadas_cab' => $model->id_sys_rrhh_cuadrillas_jornadas_cab, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sys-rrhh-cuadrillas-jornadas-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
