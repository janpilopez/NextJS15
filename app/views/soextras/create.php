<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSoextras */

$this->title = 'Registrar Solicitud de Horas Extras';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud de Horas Extras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-soextras-create">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet,
        'update'=> $update,
        'esupdate'=> $esupdate,
    ]) ?>

</div>
