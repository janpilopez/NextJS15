<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhRubrosGastos */

$this->title = 'Rubros Gastos';
$this->params['breadcrumbs'][] = ['label' => 'Rubros Gastos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_rrhh_rubros_gastos' => $model->id_sys_rrhh_rubros_gastos]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-rubros-gastos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
