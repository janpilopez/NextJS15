<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSoextras */

$this->title = 'Solicitud Horas Extras';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud Horas Extras', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cuadrilla, 'url' => ['view', 'id_sys_rrhh_cuadrilla' => $model->id_sys_rrhh_cuadrilla, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-soextras-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'update'=> $update,
        'esupdate'=> $esupdate
    ]) ?>

</div>
