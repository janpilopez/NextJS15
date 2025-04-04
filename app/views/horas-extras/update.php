<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHextrasCab */

$this->title = 'Horas Extras';
$this->params['breadcrumbs'][] = ['label' => 'Horas Extras', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_hextras, 'url' => ['view', 'id_sys_rrhh_hextras' => $model->id_sys_rrhh_hextras, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-hextras-cab-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
    ]) ?>
</div>
