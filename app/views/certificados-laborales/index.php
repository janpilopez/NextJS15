<?php

use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysAdmUsuariosDep;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhPermisoAlimentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Certificados Laborales';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-certificados-laborales-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Nueva Solicitud', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
  
            'id',
            'id_sys_rrhh_cedula',
            [
                //'header' => 'Estado',
                'attribute'=> 'nombres',
                'value' => function ($model) {
                   $empreados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                
                return $empreados->nombres;
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
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso','N'=> 'No Aprobado'],
                'value' => function ($model) {
                if ($model->estado == 'P') {
                    return Html::a('En Proceso', [''], [
                        'class' => 'btn btn-xs btn-warning btn-block',
                    ]);
                }elseif($model->estado == 'A'){
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
                
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{anular}&nbsp{aprobar}',
                    'buttons'=>[
                        
                        'view' => function ($url, $model) {
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                            if( $ids == 'N' || $ids == 'S' || $ids == 'M' || $ids == 'A' || $ids == 'J'){
                                if($model->estado == 'A'){
                                    return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                        'title' => 'Imprimir Solicitud',
                                    ]);
                                }
                            }else{
                                
                                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                    'title' => 'Imprimir Solicitud',
                                ]);
                            }
                           
                        },
                        'update' => function($url, $model){
                            
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                'title' => 'Ver',
                            ]);
                        },
                    
                       
                        'anular' => function($url, $model){
                        
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                            
                            if( $ids == 'N' || $ids == 'S' || $ids == 'M' || $ids == 'A' || $ids == 'J'){
                                
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
                            
                            if( $ids == 'N' || $ids == 'S' || $ids == 'M' || $ids == 'A' || $ids == 'J'){
                                
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
                            
                            return ['certificados-laborales/view','id'=>$data->id];
                        }
                        
                        if($action == 'update'){
                            return ['certificados-laborales/update','id'=>$data->id];
                        }
                        
                        if($action =='anular'){
                            
                            return ['certificados-laborales/anularsolicitud','id'=>$data->id];
                        }
                        if($action =='aprobar'){
                            
                            return ['certificados-laborales/aprobarsolicitud','id'=>$data->id];
                        }
                        }
                        ],
        
        ],
    ]); ?>


</div>
