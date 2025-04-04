<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedor */

$this->title = 'Horarios Comedor';
$this->params['breadcrumbs'][] = ['label' => 'Horarios Comedor', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-comedor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id_sys_rrhh_comedor' => $model->id_sys_rrhh_comedor, 'id_sys_empresa' => $model->id_sys_empresa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id_sys_rrhh_comedor' => $model->id_sys_rrhh_comedor, 'id_sys_empresa' => $model->id_sys_empresa], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_rrhh_comedor',
            'alimento',
            'h_desde',
            'h_hasta',
            'tiempo_descuento',
            //'id_sys_empresa',
        ],
    ]) ?>

</div>
