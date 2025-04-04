<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmActividades */

$this->title = 'Activiades';
$this->params['breadcrumbs'][] = ['label' => 'Actividades', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_actividad' => $model->id_sys_adm_actividad]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-adm-actividades-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
