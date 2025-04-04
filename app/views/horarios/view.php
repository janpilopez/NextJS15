<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhHorarioCab */

$this->title = 'Horarios Laborales';
$this->params['breadcrumbs'][] = ['label' => 'Horarios Laborales', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-horario-cab-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_sys_rrhh_horario_cab], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id_sys_rrhh_horario_cab], [
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
           // 'id_sys_rrhh_horario_cab',
            'horario',
            'hora_inicio',
            'hora_fin',
            'hora_lunch',
           // 'estado',
            //'id_sys_empresa',
        ],
    ]) ?>

</div>
