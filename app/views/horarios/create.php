<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHorarioCab */

$this->title = 'Horarios Laborales';
$this->params['breadcrumbs'][] = ['label' => 'Horarios Laborales', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-horario-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
    ]) ?>

</div>
