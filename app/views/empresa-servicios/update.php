<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpresaServicios */

$this->title = 'Actualizar Empresa';
$this->params['breadcrumbs'][] = ['label' => 'Empresa Servicios', 'url' => ['index']];
?>
<div class="sys-rrhh-empresa-servicios-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
