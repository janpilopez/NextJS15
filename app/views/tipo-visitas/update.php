<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysGrupoAutorizacion */

$this->title = 'Actualizar Tipo Visita';
$this->params['breadcrumbs'][] = ['label' => 'Tipo de Visita', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar Tipo de Visita';
?>
<div class="sys-acceso-tipos-visitas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
