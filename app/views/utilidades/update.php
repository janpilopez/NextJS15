<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhUtilidadesCab */

$this->title = 'Actualizar Utilidades';
$this->params['breadcrumbs'][] = ['label' => 'Utilidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-utilidades-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
