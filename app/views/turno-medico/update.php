<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Atención Turno # ' . $model->numero;
$this->params['breadcrumbs'][] = ['label' => 'Turnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Atención';
?>
<div class="sys-med-turno-medico-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
