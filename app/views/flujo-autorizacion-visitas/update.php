<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysUserGrupoAutorizacion */

$this->title = 'Actualizar Grupo de Autorización';
$this->params['breadcrumbs'][] = ['label' => 'Flujo de Autorizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-user-grupo-autorizacion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'updated'=> true
    ]) ?>

</div>
