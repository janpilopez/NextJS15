<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhMareasCab */

$this->title = 'Actualizar Marea';
$this->params['breadcrumbs'][] = ['label' => 'Mareas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_mareas_cab, 'url' => ['view', 'id' => $model->id_sys_rrhh_mareas_cab]];
$this->params['breadcrumbs'][] = 'Mareas';
?>
<div class="sys-rrhh-mareas-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'update' => $update
    ]) ?>

</div>
