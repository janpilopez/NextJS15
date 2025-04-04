<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSotaxis */

$this->title = 'Solicitud Taxis';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud Taxis', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cuadrilla, 'url' => ['view', 'id_sys_rrhh_cuadrilla' => $model->id_sys_rrhh_cuadrilla, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-sotaxis-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'update'=> $update,
        'esupdate'=> $esupdate
    ]) ?>

</div>
