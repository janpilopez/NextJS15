<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologiaCategoria */
$this->title = 'Patología - Categoría';
$this->params['breadcrumbs'][] = ['label' => 'Patología Categorías', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-patologia-categoria-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'categoria',
            //'activo',
        ],
    ]) ?>

</div>
