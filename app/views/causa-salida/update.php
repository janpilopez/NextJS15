<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCausaSalida */

$this->title = 'Causas Salidas';
$this->params['breadcrumbs'][] = ['label' => 'Causas Salidas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_rrhh_causa_salida' => $model->id_sys_rrhh_causa_salida]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-causa-salida-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
