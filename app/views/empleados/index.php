<?php

use app\models\SysRrhhEmpleadosFoto;
use app\models\SysAdmUsuariosDep;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmAreas;
use app\models\User;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Empleados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empleados-index">

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

     
            
            [
                'label'=>'Cedula',
                'attribute'=>'id_sys_rrhh_cedula',
                'contentOptions'=>['style'=>'width: 10%;'],
                ],
            
           // 'tipo_identificacion',
            'nombres',
           // 'id_sys_empresa',
           // 'id_sys_adm_cargo',
           
               [
                    'label'=>'Area',
                    'attribute'=>'area',
                    'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->orderBy('area')->all(), 'id_sys_adm_area', 'area'),
                    'format' => 'raw',
                    'value'=> function($model){
                    
                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->sysAdmCargo->id_sys_adm_departamento])->one();
                    $area         = SysAdmAreas::find()->where(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                    return $area->area;
                    
                    }
                ],
           
                [
                    'label'=>'Departamento',
                    'attribute'=>'departamento',
                    'filter'=> ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=>'A'])->orderBy('departamento')->all(), 'id_sys_adm_departamento', 'departamento') ,
                    'format' => 'raw',
                    'value'=> function($model){
                      
                    $departamento = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $model->sysAdmCargo->id_sys_adm_departamento])->one();
                    
                    return $departamento->departamento ;
                    }
                ],
            
            [
                'label'=>'Cargo ',
                'attribute'=>'id_sys_adm_cargo',
                'filter'=>false,
                'format' => 'raw',
                'value'=> function($model){
                         return $model->sysAdmCargo->cargo;
                }
             ],
            //'estado',
            //'fecha_nacimiento',
            //'estado_civil',
            //'genero',
            //'telefono',
            //'celular',
            //'email:email',
            //'id_sys_parroquia',
            //'direccion',
            //'formacion_academica',
            //'titulo_academico',
            //'calificacion_positiva',
            //'discapacidad',
            //'tipo_discapacidad',
            //'por_discapacidad',
            //'ide_discapacidad',
            //'tipo_sangre',
            //'id_sys_rrhh_contrato',
            //'id_sys_adm_actividad',
            //'id_sys_adm_ccosto',
            //'tipo_empleado',
            //'lunch',
            //'valor_lunch',
            //'desayuno',
            //'almuerzo',
            //'merienda',
            //'transporte',
            //'valor_transporte',
            //'id_sys_adm_ruta',
            //'decimo',
            //'freserva',
            //'id_sys_rrhh_banco',
            //'id_sys_rrhh_forma_pago',
            //'cta_banco',
            //'num_tar',
            //'transaccion_usuario',
        
                
                [
                    'label'=>'Estado',
                    'attribute'=>'estado',
                    'contentOptions'=>['style'=>'width: 3%;'],
                    'filter'=> ['A' => 'Activo','I' => 'Inactivo'],
                    'format' => 'raw',
                    'value'=> function($model){
                        
                    if ($model->estado== 'A') {
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
                   
                         $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
                   
                        if ($fotos) :
                               /// return  Html::img('data:image/jpeg;base64, '.$fotos, ['style'=>"width:70px;height:70px;"]);
                             
                         return  Html::img('data:image/jpeg;base64, '.$fotos['baze64'], ['style'=>"width:70px;height:70px;"]);
                           
                              
                        else :
                              return  Html::img(Yii::$app->homeUrl."img/sin_foto.jpg", ['style'=>"width:70px;height:70px;"]);
                        endif;
                        
                    }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{update}&nbsp{updatebloqueo}&nbsp{credencial}&nbsp{contrato}',
                        'buttons'=>[
                            
                           'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', $url, [
                                'title' => 'Ver',
                                
                            ]);
                            },
                            'update' => function($url, $model){
                            
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => 'Actualizar',
                                
                            ]);
                            },

                            'updatebloqueo' => function($url, $model){
                            
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => 'Datos',
                                    
                                ]);
                            },
                            
                            
                           /* 'delete' => function($url, $model){
                            
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => 'Anular Periodo',
                                'data' => [
                                    'confirm' => 'Está seguro que desea anular el siguiente registro?',
                                    'method' => 'post',
                                ],
                                
                            ]);
                            },
                            */

                            'contrato' => function($url, $model){

                                $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                                $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                                
                                if( $ids == 'D' ){
                                    return Html::a('<span class="glyphicon glyphicon-file"></span>', $url, [
                                        'title' => 'Contrato',
                                        
                                    ]);
                                }else{
                                    
                                }   
   
                            },

                            'credencial' => function($url, $model){
                            
                             return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                'title' => 'Credencial',
                                 'target'=> '_blank'
                                
                            ]);
                            },
                            
                          
                            
                            ],
                            'urlCreator'=>function($action,$data){
                            if($action=='view'){
                                
                                if(User::hasRole('MEDICO')):
                                
                                  return ['ficha-medica/create','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula];
                               
                                 //  return ['empleados/view','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                                else:
                                         return ['empleados/view','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                endif;
                                
                            }
                            
                            if($action == 'update'){
                                
                                return ['empleados/update','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }

                            if($action == 'updatebloqueo'){
                                
                                return ['empleados/updatebloqueo','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            /*
                            if($action =='delete'){
                                
                                return ['rol/delete','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }
                            */

                            if($action =='contrato'){
                                
                                return ['empleados/contrato','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
                            }

                            if($action =='credencial'){
                                
                                return ['empleados/imprimecredencial','id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula, 'id_sys_empresa'=> $data->id_sys_empresa];
                                
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
