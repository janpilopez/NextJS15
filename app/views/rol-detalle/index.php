<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhEmpleadosRolMovSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ajustar Rol Detalle';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-rol-mov-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'anio',
            //'mes',
            [
                'label'=>'mes',
                'attribute'=>'mes',
                'format' => 'raw',
                'value'=> function($model){
                
                     return Yii::$app->params['meses'][$model->mes];     
                
                }
                
            ],
           // 'periodo',
            [
                'label'=>'Periodo',
                'attribute'=>'periodo',
                'format' => 'raw',
                'value'=> function($model){
                
                   return Yii::$app->params['periodos'][$model->periodo];
                }
                
                ],
            
            'id_sys_rrhh_cedula',
            'id_sys_rrhh_concepto',
            //'unidad',
            'cantidad',
            'valor',
            //'id_sys_empresa',
            //'transaccion_usuario',
            //'estado',
            //'id_sys_adm_departamento',

            ['class' => 'yii\grid\ActionColumn'],
            
        ],
    ]); ?>


</div>
