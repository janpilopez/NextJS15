<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedorVisitas */

$this->title = $model->id_sys_rrhh_comedor_visita;
$this->params['breadcrumbs'][] = ['label' => 'Sys Rrhh Comedor Visitas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-comedor-visitas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id_sys_rrhh_comedor_visita' => $model->id_sys_rrhh_comedor_visita, 'id_sys_empresa' => $model->id_sys_empresa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id_sys_rrhh_comedor_visita' => $model->id_sys_rrhh_comedor_visita, 'id_sys_empresa' => $model->id_sys_empresa], [
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
            'tipo_visita',
            'desayuno',
            'almuerzo',
            'merienda',
            'id_sys_rrhh_comedor_visita',
            'codigo',
            'id_sys_adm_departamento',
            'id_sys_empresa',
            'estado',
        ],
    ]) ?>

</div>
