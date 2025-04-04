<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPrestamosCab */

$this->title = 'Registrar Préstamo';
$this->params['breadcrumbs'][] = ['label' => 'Préstamos Empresa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-prestamos-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'meses'=> $meses,
        'secuencia'=> $secuencia,
        'periododes'=> $periododes,
        'update'=> $update
    ]) ?>

</div>
