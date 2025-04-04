<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmRutas */

$this->title = 'Rutas de Transporte';
$this->params['breadcrumbs'][] = ['label' => 'Rutas de Rutas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_ruta' => $model->id_sys_adm_ruta]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-rutas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
