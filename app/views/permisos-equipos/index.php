<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysRrhhEmpleados;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosPermisosEquiposSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Permisos Equipos Informáticos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-empleados-permisos-equipos-index">

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
            'motivo',
            'fecha_inicio',
            'fecha_fin',
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
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso', 'No Aprobado'],
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
                
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}',
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
                        
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
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
                            
                            return ['permisos-equipos/view','id'=>$data->id];
                        }
                        
                        if($action == 'update'){
                            return ['permisos-equipos/update','id'=>$data->id];
                        }
                        
                        if($action =='delete'){
                            
                            return ['permisos-equipos/delete','id'=> $data->id];
                        }
                        if($action =='aprobar'){
                            
                            return ['permisos-equipos/aprobar','id'=> $data->id ];
                        }
                        }
                        ],
        ],
    ]); ?>


</div>
