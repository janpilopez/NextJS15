<?php

use app\models\SysAdmPeriodoVacaciones;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleadosPeriodoVacaciones;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhVacacionesSolicitudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitud de Vacaciones';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 

$per = [];

$periodosSolicitud = SysRrhhEmpleadosPeriodoVacaciones::find()->select('id_sys_adm_periodo_vacaciones')->groupBy('id_sys_adm_periodo_vacaciones')->all();

foreach($periodosSolicitud as $periodos){
    array_push($per,$periodos->id_sys_adm_periodo_vacaciones);
}

?>
<div class="sys-rrhh-vacaciones-solicitud-index">

    <h1><?= Html::encode($this->title) ?></h1>
     <?= Html::a('Registrar Solicitud', ['create'], ['class' => 'btn btn-success']) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_rrhh_vacaciones_solicitud',
           
            [
                'label'=>'Cedula',
                'attribute'=>'cedula',
                'format' => 'raw',
                'value'=> function($model){
                
                   return $model->empleado->id_sys_rrhh_cedula;
                
                }
            ],
            
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'format' => 'raw',
                'value'=> function($model){
                
                   return $model->empleado->nombres;
                
                }
             ],
            
            //'id_sys_rrhh_vacaciones_periodo',
            [
                'label'=>'Período',
                'attribute'=>'periodo',
                'filter'=> ArrayHelper::map(SysAdmPeriodoVacaciones::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_periodo_vacaciones'=>$per])->orderBy('anio_vac asc')->all(), 'id_sys_adm_periodo_vacaciones', 'periodo'),
                'format' => 'raw',
                'value'=> function($model){
                
                    return $model->periodo->periodo;
                
                }
             ],
             
          
            
            [
                'attribute' => 'fecha_inicio',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_inicio',
                    'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]
                    ),
                'format' => 'raw',
                'contentOptions'=>['style'=>'width: 10%;']
            ],
            
            
            [
                'attribute' => 'fecha_fin',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_fin',
                    'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]
                    ),
                'format' => 'raw',
              
            ],
            
            [
                'attribute' => 'fecha_registro',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_registro',
                    'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]
                    ),
                'format' => 'raw',
              
            ],
            [
                //'header' => 'Estado',
                'attribute'=> 'estado_solicitud', 
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso', 'N'=> 'No Aprobado'],
                'value' => function ($model) {
                if ($model->Estadosolicitud == 'P') {
                    return Html::a('En Proceso', [''], [
                        'class' => 'btn btn-xs btn-warning btn-block',
                    ]);
                }elseif($model->Estadosolicitud == 'A'){
                    return Html::a('Aprobado',[''], [
                        'class' => 'btn btn-xs btn-success btn-block',
                    ]);
                }else{
                    return Html::a('No Aprobado',[''], [
                        'class' => 'btn btn-xs btn-danger btn-block',
                    ]);
                }
                },
                'format' => 'raw',
                'contentOptions'=>['style'=>'width: 5%;']
                ],
            //'fecha_fin',
            //'fecha_registro',
            //'comentario',
            //'id_sys_rrhh_cedula',
            //'id_sys_empresa',
            //'estado',

           // ['class' => 'yii\grid\ActionColumn'],
           
                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{delete}&nbsp{aprobar}',
                    'buttons'=>[
                        
                        'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                            'title' => 'Imprimir Solicitud',
                            
                        ]);
                        },
                        'update' => function($url, $model){
                        
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                            
                            if( $ids == 'N' || $ids == 'S'){
                                
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => 'Ver',
                                ]);
                            }
                        },
                    
                       
                        'delete' => function($url, $model){
                        
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                            
                            if( $ids == 'N' || $ids == 'S'){
                                
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => 'Anular Solicitud',
                                    'data' => [
                                        'confirm' => 'Está seguro que desea anular el siguiente registro?'
                                    ],
                                    
                                ]);
                            }
                        },
                       'aprobar' => function($url, $model){
                        
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                            
                            if( $ids == 'N' || $ids == 'S'){
                                
                            }else{
                                return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                                        'title' => 'Aprobar Solicitud',
                                        'data' => [
                                        'confirm' => 'Está seguro de aprobar la solicitud?',
                                        'method' => 'post',
                                    ],
                                    
                                ]);
                            }
                        
                        },
                        
                        
                        ],
                        'urlCreator'=>function($action,$data){
                        if($action=='view'){
                            
                            return ['solicitud-vacaciones/view','id_sys_rrhh_vacaciones_solicitud'=>$data->id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa'=> $data->id_sys_empresa];
                        }
                        
                        if($action == 'update'){
                            return ['solicitud-vacaciones/update','id_sys_rrhh_vacaciones_solicitud'=>$data->id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa'=> $data->id_sys_empresa];
                        }
                        
                        if($action =='delete'){
                            
                            return ['solicitud-vacaciones/delete','id_sys_rrhh_vacaciones_solicitud'=> $data->id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa'=> $data->id_sys_empresa ];
                        }
                        if($action =='aprobar'){
                            
                            return ['solicitud-vacaciones/aprobarsolicitud','id_sys_rrhh_vacaciones_solicitud'=> $data->id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa'=> $data->id_sys_empresa ];
                        }
                        }
                        ],
                
                
        ],
    ]); ?>
</div>
