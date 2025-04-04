<?php

use app\models\SysAccesoTipoVisitas;
use app\models\SysRrhhEmpleadosPermisosIngresos;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhEmpleados;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosPermisosingresosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Permisos Visitas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empleados-permisos-ingresos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nuevo Permiso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'empresa',
            [
                'label'=> 'Tipo Visita',
                 'filter'=> ArrayHelper::map(SysAccesoTipoVisitas::find()->all(), 'id_tipo_visita', 'tipo_visita'),
                'attribute'=>'tipo_visita',
                'format' => 'raw',
                'value'=> function($model){
                      $grupo =  SysAccesoTipoVisitas::find()->where(['id_tipo_visita'=>$model->tipo_visita])->one();
                     if($grupo):
                           return $grupo->tipo_visita;
                      endif;
                  } ,
            ],
            'observacion',
            [
                'attribute' => 'fecha_ingreso',
                'filter' => DatePicker::widget([
                   
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_ingreso',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>['class'=>'form-control']
                ]),
                   
                'format' => 'raw',
               
            ],
            [
                //'header' => 'Estado',
                'attribute'=> 'hora_ingreso',
                'value' => function ($model) {
                    return date('H:i', strtotime($model->hora_ingreso));
                },
                'format' => 'raw',
                
            ],
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_aprobacion',
            //'fecha_aprobacion',
            //'usuario_anulacion',
            //'estado',
            //'anuladado',

            [
                //'header' => 'Estado',
                'attribute'=> 'estado',
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso', 'N' =>'No Aprobado','E' =>'Atendiendo','F'=>'Atendido'],
                'value' => function ($model) {
                    if ($model->estado == 'P') {
                        return Html::a('En Proceso', [''], [
                            'class' => 'btn btn-xs btn-warning btn-block',
                        ]);
                    }elseif($model->estado == 'A'){
                        return Html::a('Aprobado',[''], [
                            'class' => 'btn btn-xs btn-success btn-block',
                        ]);
                    }elseif($model->estado == 'E'){
                        return Html::a('Atendiendo',[''], [
                            'class' => 'btn btn-xs btn-warning btn-block',
                        ]);
                    }elseif($model->estado == 'F'){
                        return Html::a('Atendido',[''], [
                            'class' => 'btn btn-xs btn-success btn-block',
                        ]);
                    }elseif($model->estado == 'N'){
                        return Html::a('No Aprobado',[''], [
                            'class' => 'btn btn-xs btn-danger btn-block',
                        ]);
                    }
                },
                'format' => 'raw',
                
            ],

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{aprobar}&nbsp{delete}',
                    'buttons'=>[
                        
                        'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Ver Permiso',
                            
                        ]);
                        },
                        'update' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => 'Actualizar',
                            
                        ]);
                        },
                        
                        
                        'delete' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                            'title' => 'Anular Permiso',
                            'data' => [
                                'confirm' => 'Está seguro que desea anular el siguiente registro?',
                                'method' => 'post',
                            ],
                            
                        ]);
                        },
                        'aprobar' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                            'title' => 'Aprobar Permiso',
                            'data' => [
                                'confirm' => 'Está seguro de aprobar el permiso?',
                                'method' => 'post',
                            ],
                            
                        ]);
                        },
                        
                        
                        ],
                        'urlCreator'=>function($action,$data){
                            if($action=='view'){
                                
                                return ['permisos-ingresos/view','id'=>$data->id];
                            }
                            
                            if($action == 'update'){
                                return ['permisos-ingresos/update','id'=>$data->id];
                            }
                            
                            if($action =='delete'){
                                
                                return ['permisos-ingresos/delete','id'=> $data->id];
                            }
                            if($action =='aprobar'){
                                
                                return ['permisos-ingresos/aprobar','id'=> $data->id ];
                            }
                        }
                ],
        ],
    ]); ?>


</div>
