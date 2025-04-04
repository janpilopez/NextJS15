<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Indicadores Sistemas';
$this->params['breadcrumbs'][] = ['label' => 'Indicadores Sistemas', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-adm-areas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'update' => $update
    ]) ?>

</div>
