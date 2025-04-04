<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysSsooIncidente */

$this->title = 'Actualizar Incidente';
$this->params['breadcrumbs'][] = ['label' => 'SSOO Incidentes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-ssoo-incidente-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
