<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPrestamosCab */

$this->title = 'Actualizar Préstamo';
$this->params['breadcrumbs'][] = ['label' => 'Préstamos Empresa', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar Préstamos';
?>
<div class="sys-rrhh-prestamos-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'meses'=> $meses,
        'secuencia'=> $secuencia,
        'periododes'=> $periododes,
        'update' => $update
    ]) ?>

</div>
