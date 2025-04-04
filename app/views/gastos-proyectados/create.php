<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Gastos Proyectados';
$this->params['breadcrumbs'][] = ['label' => 'Gastos Proyectados', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-empleados-gastos-proyectados-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet,
        'update' => $update,
    ]) ?>

</div>
