<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhFiniquitoCab */

$this->title = 'Registrar Finiquito';
$this->params['breadcrumbs'][] = ['label' => 'Finiquitos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-finiquito-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet
    ]) ?>

</div>
