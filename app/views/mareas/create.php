<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhMareasCab */

$this->title = 'Registrar Marea';
$this->params['breadcrumbs'][] = ['label' => 'Mareas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-mareas-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
        'update' => $update,
    ]) ?>

</div>
