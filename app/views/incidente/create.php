<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysSsooIncidente */

$this->title = 'Registrar Incidente';
$this->params['breadcrumbs'][] = ['label' => 'SSOO Incidentes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-ssoo-incidente-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
