<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisos */

$this->title = 'Tipos de Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Tipo de Permisos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_permiso, 'url' => ['view', 'id_sys_rrhh_permiso' => $model->id_sys_rrhh_permiso, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-permisos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
