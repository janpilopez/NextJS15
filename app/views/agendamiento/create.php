<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillasJornadasCab */

$this->title = 'Registrar Agendamiento';
$this->params['breadcrumbs'][] = ['label' => 'Agendamiento Laboral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-cuadrillas-jornadas-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

     <?= $this->render('_agendamiento') ?>

</div>
