<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Equipo de protección personal';
$this->params['breadcrumbs'][] = ['label' => 'Listado Maestro EPP', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_area' => $model->id_sys_adm_area]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-ssoo-epp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'inputDisable' => $inputDisable
    ]) ?>

</div>
