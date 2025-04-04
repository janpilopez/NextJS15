<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhImpuestoRenta */

$this->title = 'Impuesto Renta';
$this->params['breadcrumbs'][] = ['label' => 'Impuesto Renta', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_rrhh_impuesto_renta' => $model->id_sys_rrhh_impuesto_renta]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-impuesto-renta-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
