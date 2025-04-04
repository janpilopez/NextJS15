<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisoAlimentos */

$this->title = 'Update Sys Rrhh Permiso Alimentos: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Permiso Alimentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sys-rrhh-permiso-alimentos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
