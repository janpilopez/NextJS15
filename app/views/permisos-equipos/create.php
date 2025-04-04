<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosPermisosEquipos */

$this->title = 'Permisos Equipos Informáticos';
$this->params['breadcrumbs'][] = ['label' => 'Permisos Equipos Informáticos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-permisos-equipos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet
    ]) ?>

</div>
