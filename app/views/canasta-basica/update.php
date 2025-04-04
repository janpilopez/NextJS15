<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */

$this->title = 'Actualizar Registro';
$this->params['breadcrumbs'][] = ['label' => 'Canasta Básica', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar Registro';
?>
<div class="sys-adm-canasta-basica-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
