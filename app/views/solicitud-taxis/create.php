<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSotaxis */

$this->title = 'Registrar Solicitud de Taxis';
$this->params['breadcrumbs'][] = ['label' => 'Solicitud de Taxis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-sotaxis-create">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet,
        'update'=> $update,
        'esupdate'=> $esupdate,
    ]) ?>

</div>
