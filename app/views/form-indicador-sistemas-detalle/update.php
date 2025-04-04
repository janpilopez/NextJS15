<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Formulario Indicador Sistemas';
$this->params['breadcrumbs'][] = ['label' => 'Formulario Indicador Sistemas', 'url' => ['index','id_encabezado_indicador'=>$model->id_encabezado_indicador]];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_empresa, 'url' => ['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_adm_area' => $model->id_sys_adm_area]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-indicador-sistemas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet,
        'id_encabezado_indicador' => $model->id_encabezado_indicador,
        'update' => $update
    ]) ?>

</div>
