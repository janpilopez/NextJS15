<?php

use app\models\SysRrhhEmpresaServicios;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpresaServiciosEmpleadosSearch*/
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Empresa Servicios Empleados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empresa-servicios-empleados-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Empleado', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_cedula',
            //'id_sys_empresa',
            //'id_sys_rrhh_empresa_servicios',
            [
                'label'=>'Empresa',
                'attribute'=>'id_sys_rrhh_empresa_servicios',
                'filter'=>  ArrayHelper::map(SysRrhhEmpresaServicios::find()->orderBy('nombre')->all(), 'id_sys_rrhh_empresa_servicio', 'nombre'),
                'format' => 'raw',
                'value'=> function($model){
                    
                    $empresa = SysRrhhEmpresaServicios::find()->where(['id_sys_rrhh_empresa_servicio'=> $model->id_sys_rrhh_empresa_servicios])->one();
                      
                      if($empresa):
                          return $empresa->nombre;
                      else:
                          return "S/N";
                      endif;
                
                }
                ],
            'nombres',
            //'genero',
            //'ocupacion',
            //'fecha_ingreso',
            //'fecha_salida',
            //'estado',
            'cargas_familiares',
            'dias_laborados',
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'filter'=>  ['A'=> 'Activo', 'I'=> 'Inactivo' ],
                'format' => 'raw',
                'value'=> function($model){
                
                     return $model->estado == 'A' ? 'Activo' : 'Inactivo';
                
                }
                ],
            //'faltas',
            //'usuario_registro',
            //'fecha_registro',
            //'usuario_actualizacion',
            //'fecha_actualizacion',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{update}',
                'buttons'=>[
                    'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => 'Ver',
                        
                    ]);
                    },
                    'update' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => 'Actualizar',
                        
                    ]);
                    },
                    ],
                    'urlCreator'=>function($action,$data){
                        if($action=='view'){
                            return ['empresa-empleados/view','id'=>$data->id_sys_rrhh_cedula];
                            
                            } 
                        if($action == 'update'){
                            return ['empresa-empleados/update','id'=>$data->id_sys_rrhh_cedula];
                            }
                        }
                    ],
        ],
    ]); ?>


</div>
