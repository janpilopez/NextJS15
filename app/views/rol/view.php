<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleadosRolCab */

$this->title = 'Ver Periodo';
$this->params['breadcrumbs'][] = ['label' => 'Periodos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-empleados-rol-cab-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_empresa' => $model->id_sys_empresa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_empresa' => $model->id_sys_empresa], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro que quiere eliminar el periodo?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'anio',
            [
                'label'=>'mes',
                'attribute'=>'mes',
                'value'=> function($model){
                   $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
                   return  $meses[$model->mes];
                } 
             ],
            'periodo',
            'fecha_registro',
            [
                'label'=>'estado',
                'attribute'=>'estado',
                'value'=> function($model){
                        return  $model->estado == 'Q' ? 'Generado': 'Liquidado';
                } 
             ],
            'fecha_ini',
            'fecha_fin',
            'fecha_ini_liq',
            'fecha_fin_liq',

            
        ],
    ]) ?>

</div>
