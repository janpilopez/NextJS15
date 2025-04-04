<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFiniquitoCab */

$this->title = 'Actualizar Finiquito';
$this->params['breadcrumbs'][] = ['label' => 'Finiquitos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-rrhh-finiquito-cab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet
    ]) ?>

</div>
