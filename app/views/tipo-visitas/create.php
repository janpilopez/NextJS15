<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysGrupoAutorizacion */

$this->title = 'Registrar Tipo';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Visita', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-acceso-tipos-visitas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
