<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysConfiguracion */

$this->title = 'Parámetro Password';
$this->params['breadcrumbs'][] = ['label' => 'Parámetro Password', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-configuracion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
