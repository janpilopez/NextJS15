<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmCargos */

$this->title = 'Cargos';
$this->params['breadcrumbs'][] = ['label' => 'Cargos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Registrar';
?>
<div class="sys-adm-cargos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
