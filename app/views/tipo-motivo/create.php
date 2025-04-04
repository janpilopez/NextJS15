<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Registrar Motivo de Atención';
$this->params['breadcrumbs'][] = ['label' => 'Motivos de Atención', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-tipo-motivo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
