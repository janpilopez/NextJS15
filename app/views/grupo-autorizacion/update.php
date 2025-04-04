<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysGrupoAutorizacion */

$this->title = 'Actualizar Grupo';
$this->params['breadcrumbs'][] = ['label' => 'Grupo Autorización', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar Grupo';
?>
<div class="sys-grupo-autorizacion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
