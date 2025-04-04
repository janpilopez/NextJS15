<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedCertficadoMedico */

$this->title = 'Registrar Certificado Médico';
$this->params['breadcrumbs'][] = ['label' => 'Certficado Médicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-certficado-medico-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'inputDisable' => $inputDisable,
        'listpermisos'=> $listpermisos,
    ]) ?>

</div>
