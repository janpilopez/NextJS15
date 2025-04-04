<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhConceptos */

$this->title = 'Coneceptos de Nómina';
$this->params['breadcrumbs'][] = ['label' => 'Conceptos de Nómina', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registar';
?>
<div class="sys-rrhh-conceptos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bloqueado' => $bloqueado,
    ]) ?>

</div>
