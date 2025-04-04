<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */

$this->title = 'Nuevo Registro';
$this->params['breadcrumbs'][] = ['label' => 'Historial Sueldos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-adm-historial-sueldo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
