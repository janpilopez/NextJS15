<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Eventos/Capacitaciones';
$this->params['breadcrumbs'][] = ['label' => 'Eventos/Capacitaciones', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_area' => $model->id_sys_adm_area]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-areas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
