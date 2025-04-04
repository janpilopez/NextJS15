<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedCertificadoSalud */

$this->title = 'Registrar Certificado de Salud';
$this->params['breadcrumbs'][] = ['label' => 'Certificado Salud', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-certificado-salud-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
