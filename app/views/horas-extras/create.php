<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHextrasCab */

$this->title = 'Horas Extras';
$this->params['breadcrumbs'][] = ['label' => 'Horas Extras', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-hextras-cab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet'=> $modeldet,
    ]) ?>

</div>
