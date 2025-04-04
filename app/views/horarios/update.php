<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHorarioCab */

$this->title = 'Horarios Laborales';
$this->params['breadcrumbs'][] = ['label' => 'Horarios Laborales', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_horario_cab, 'url' => ['view', 'id' => $model->id_sys_rrhh_horario_cab]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-horario-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
    ]) ?>

</div>
