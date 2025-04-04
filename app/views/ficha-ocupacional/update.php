<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaOpupacional */

$this->title = 'Actualizar Ficha Ocupacional # ' . $model->secuencial;
$this->params['breadcrumbs'][] = ['label' => 'Ficha Ocupacional', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-ficha-opupacional-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
