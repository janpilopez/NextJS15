<?php

use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhPermisoAlimentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Permiso Alimentos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-permiso-alimentos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Nuevo Permiso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
  
            'id',
            [
                'label'=>'Cedula',
                'attribute'=>'id_sys_rrhh_cedula',
            ],
            [
                //'header' => 'Estado',
                'attribute'=> 'nombres',
                'value' => function ($model) {
                   $empreados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                
                return $empreados->nombres;
                },
                'format' => 'raw',
                
                ],
            'inicio',
            'fin',
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
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso'],
                'value' => function ($model) {
                if ($model->estado == 'P') {
                    return Html::a('En Proceso', [''], [
                        'class' => 'btn btn-xs btn-warning btn-block',
                    ]);
                }elseif($model->estado == 'A'){
                    return Html::a('Aprobado',[''], [
                        'class' => 'btn btn-xs btn-success btn-block',
                    ]);
                }
                },
                'format' => 'raw',
                
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{delete}',
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
                            
                            return ['permiso-alimento/view','id'=>$data->id];
                        }
                        
                        if($action == 'update'){
                            return ['permiso-alimento/update','id'=>$data->id];
                        }
                        
                        if($action =='delete'){
                            
                            return ['permiso-alimento/delete','id'=> $data->id];
                        }
                        if($action =='aprobar'){
                            
                            return ['permiso-alimento/aprobar','id'=> $data->id ];
                        }
                        }
                        ],
        ],
    ]); ?>


</div>
