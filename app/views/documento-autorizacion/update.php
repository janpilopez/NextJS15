<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysDocumentoAutorizacion */

$this->title = 'Actualizar Autorización';
$this->params['breadcrumbs'][] = ['label' => 'Autorización de Documentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-documento-autorizacion-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
