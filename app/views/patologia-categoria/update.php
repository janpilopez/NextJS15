<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologiaCategoria */

$this->title = 'Actualizar Patología - Categoría';
$this->params['breadcrumbs'][] = ['label' => 'Patología Categorías', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-patologia-categoria-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
