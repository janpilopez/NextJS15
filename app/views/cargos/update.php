<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmCargos */

$this->title = 'Cargos';
$this->params['breadcrumbs'][] = ['label' => 'Cargos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_cargo' => $model->id_sys_adm_cargo]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-cargos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
