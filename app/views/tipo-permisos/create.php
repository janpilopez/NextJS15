<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisos */

$this->title = 'Tipo Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Permisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-permisos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
