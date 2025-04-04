<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhConceptos */

$this->title = 'Conceptos de Nómina';
$this->params['breadcrumbs'][] = ['label' => 'Conceptos de Nómina', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_concepto, 'url' => ['view', 'id_sys_rrhh_concepto' => $model->id_sys_rrhh_concepto, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-conceptos-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'bloqueado' => $bloqueado,
    ]) ?>
</div>
