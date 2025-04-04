<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhContratos */

$this->title = 'Contratos';
$this->params['breadcrumbs'][] = ['label' => 'Contratos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-rrhh-contratos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
