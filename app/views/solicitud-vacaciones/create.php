<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhVacacionesSolicitud */

$this->title = 'Registrar Solicitud';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-vacaciones-solicitud-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'update'=> $update
    ]) ?>
</div>
