<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhSoextras */

$this->params['breadcrumbs'][] = ['label' => 'Solicitud de Horas Extras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-soextras-create">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formcomentario', [
        'model' => $model,
        'update'=> $update,
        'esupdate'=> $esupdate,
    ]) ?>

</div>
