<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedFichaOpupacional */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sys Med Ficha Opupacionals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-ficha-opupacional-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_sys_rrhh_cedula',
            'secuencial',
            'tipo',
            'fecha',
        ],
    ]) ?>

</div>
