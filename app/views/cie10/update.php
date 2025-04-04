<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedCie10 */

$this->title = 'Actualizar Cie10';
$this->params['breadcrumbs'][] = ['label' => 'Cie10', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="sys-med-cie10-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
