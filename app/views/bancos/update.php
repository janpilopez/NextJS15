<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhBancos */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Bancos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_banco, 'url' => ['view', 'id_sys_rrhh_banco' => $model->id_sys_rrhh_banco, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-bancos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
