<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhVacacionesSolicitud */

$this->title =  $update == 2 ? 'Anular Solicitud': 'Actualizar Solicitud';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud Vacaciones', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_vacaciones_solicitud, 'url' => ['view', 'id_sys_rrhh_vacaciones_solicitud' => $model->id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar Solicitud';
?>
<div class="sys-rrhh-vacaciones-solicitud-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'update'=> $update
    ]) ?>

</div>
