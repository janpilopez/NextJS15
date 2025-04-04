<?php

use app\models\SysRrhhConceptos;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosNovedadesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Novedades Empleados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empleados-novedades-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Novededad', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
          //  'estado',
            'id_sys_rrhh_empleados_novedad',
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                return  $model->sysRrhhEmpleados->nombres;
                
                } ,
                ],
           // 'id_sys_rrhh_concepto',
            [ 'label'=>'Concepto',
                'attribute'=>'id_sys_rrhh_concepto',
                'filter'=>ArrayHelper::map(SysRrhhConceptos::find()->where(['estado'=>'A'])->all(), 'id_sys_rrhh_concepto', 'concepto'),
                'value'=> function($model){
                return $model->id_sys_rrhh_concepto;
                } ],
          //  'fecha',
            [
                'attribute' => 'fecha',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha',
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',],
                    'options'=>['class'=>'form-control input-sm']
                 ]
                    ),
                'format' => 'raw',
                'contentOptions'=>['style'=>'width: 16%;']
            ],
            //'cantidad',
            //'id_sys_empresa',
            //'transaccion_usuario',
            //'comentario',
              
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
