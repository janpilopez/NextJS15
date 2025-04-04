<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysConfiguracion */

$this->title = 'Configuraciones';
$this->params['breadcrumbs'][] = ['label' => 'Configuracion', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_conf_cod, 'url' => ['view', 'id_sys_conf_cod' => $model->id_sys_conf_cod, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-configuracion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
