<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Actualizar Certificado Médico';
$this->params['breadcrumbs'][] = ['label' => 'Certficado Médico', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-certficado-medico-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'inputDisable' => $inputDisable,
        'listpermisos'=> $listpermisos,
    ]) ?>

</div>
