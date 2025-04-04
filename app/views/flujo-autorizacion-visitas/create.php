<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysUserGrupoAutorizacion */

$this->title = 'Registrar flujo de Autorización';
$this->params['breadcrumbs'][] = ['label' => 'Flujo de Autorizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-user-grupo-autorizacion-visitas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'updated' =>false
    ]) ?>

</div>
