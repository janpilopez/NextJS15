<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysEmpresa */

$this->title = 'Actualizar Empresa: ' . $model->id_sys_empresa;
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sys-empresa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
