<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhContratos */

$this->title = 'Contratos';
$this->params['breadcrumbs'][] = ['label' => 'Contratos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_rrhh_contrato' => $model->id_sys_rrhh_contrato]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-contratos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
