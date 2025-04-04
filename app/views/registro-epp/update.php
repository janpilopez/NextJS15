<?php

use app\models\SysSsooRegistroEntregaDetalle;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Actualizar EPP';
$this->params['breadcrumbs'][] = ['label' => 'Equipos de Prot Personal', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_area' => $model->id_sys_adm_area]];
$this->params['breadcrumbs'][] = 'Actualizar';
$this->render('../_alertFLOTADOR');

?>
<div class="sys-ssoo-epp-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Agregar EPP', Url::to(['registro-epp/createadd', 'id' => $model->id_sys_ssoo_registro_entrega_epp]), ['class' => 'btn btn-success']) ?>
    </p>
    <?= $this->render('_formEdit', [
        'model' => $model,
        'inputDisable' => $inputDisable,
        'modelDetalle' => count($modelDetalle) > 0 ? $modelDetalle : [new SysSsooRegistroEntregaDetalle()],
        'listaActividades' => $listaActividades
    ]) ?>

</div>
