<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhMareasCab */

$this->title = 'Mareas';
$this->params['breadcrumbs'][] = ['label' => 'Mareas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-mareas-cab-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_sys_rrhh_mareas_cab], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id_sys_rrhh_mareas_cab], [
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
            'id_sys_rrhh_mareas_cab',
            'fecha_inicio',
            'fecha_fin',
            'tonelada',
            'valor_tonelada',
            'estado',
            'id_sys_rrhh_barcos',
          //  'usuario_creacion',
           // 'fecha_creacion',
          //  'usuario_actualizacion',
           // 'fecha_actualizacion',
        ],
    ]) ?>

</div>
