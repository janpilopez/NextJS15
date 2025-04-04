<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologiaCategoria */

$this->title = 'Registar Patólogia - Categoría';
$this->params['breadcrumbs'][] = ['label' => 'Patólogia Categorías', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-patologia-categoria-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
