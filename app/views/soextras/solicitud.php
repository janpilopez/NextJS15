<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSoextras */

$this->title = 'Registro de Solicitud de Horas Extras';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud de Horas Extras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-soextras-solicitud">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_listsolicitud',[
        'model' => $model,
        'modeldet' => $modeldet,
        'ingreso' => $ingreso,
        'update'=> $update,
    ]) ?>

</div>