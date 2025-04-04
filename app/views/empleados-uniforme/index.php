<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Registro Uniformes';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empleados-uniforme-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Uniforme', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class= 'row'>
     <div class = 'col-md-12'>
     	 <?=  Html::a('Exportar a Excel', ['uniformesxls'],['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px', 'target' => '_blank' ]);?>
     </div>
   </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label'=>'Cédula',
                'attribute'=>'id_sys_rrhh_cedula',
                'contentOptions'=>['style'=>'width: 10%;'],
                ],

            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'format' => 'raw',
                'value'=> function($model){
                
                   return $model->empleado->nombres;
                
                }
             ],
            'numero_uniforme',
            [
                'attribute' => 'fecha_entrega',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_entrega',
                  
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'],
                        'options'=>['class'=>'form-control input-sm']
                ]
                    ),
                'format' => 'raw',
                ],
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'contentOptions'=>['style'=>'width: 3%;'],
                'filter'=> [1 => 'Activo',0 => 'Inactivo'],
                'format' => 'raw',
                'value'=> function($model){
                    
                if ($model->estado== 1) {
                    return Html::a('Activo', [''], [
                        'class' => 'btn btn-xs btn-success btn-block',
                    ]);
                }else{
                    return Html::a('Inactivo',[''], [
                        'class' => 'btn btn-xs btn-warning btn-block',
                    ]);
                }
                
                }
            ],
                
                [
                    'label'=>'Foto',
                    'attribute'=>'file',
                    'contentOptions'=>['style'=>'width: 7%;'],
                    'filter'=>false,
                    'format' => 'raw',
                    'value'=> function($model){
                    
                         $db =  $_SESSION['db'];
                   
                         $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto_uniformes cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
                   
                        if ($fotos) :
                               /// return  Html::img('data:image/jpeg;base64, '.$fotos, ['style'=>"width:70px;height:70px;"]);
                             
                            return  Html::img('data:image/jpeg;base64, '.$fotos['baze64'], ['style'=>"width:70px;height:70px;"]);
                           
                              
                        else :
                            return  Html::img(Yii::$app->homeUrl."img/sin_foto.jpg", ['style'=>"width:70px;height:70px;"]);
                        endif;
                        
                    }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{update}&nbsp{delete}',
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
                            
                            
                           'delete' => function($url, $model){
                            
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => 'Inactivar',
                                'data' => [
                                    'confirm' => 'Está seguro que desea anular el siguiente registro?',
                                    'method' => 'post',
                                ],
                                
                            ]);
                            },
                            
                            /*'credencial' => function($url, $model){
                            
                             return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                'title' => 'Credencial',
                                 'target'=> '_blank'
                                
                            ]);
                            },*/
                            
                          
                            
                            ],
                            'urlCreator'=>function($action,$data){
                            
                            if($action == 'update'){
                                
                                return ['empleados-uniforme/update','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            
                            if($action =='delete'){
                                
                                return ['empleados-uniforme/delete','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            
                            if($action =='view'){
                                
                                return ['empleados-uniforme/view','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            /*
                            if($action =='liquidar'){
                                
                                return ['rol/liquidar','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            */
                            }
                            ],
        ],
    ]); ?>


</div>
