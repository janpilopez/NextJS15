<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhUtilidadesCabSearch
 **/
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Utilidades';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-utilidades-cab-index">
    <h1><?= Html::encode($this->title) ?></h1>
        <p><?= Html::a('Registrar Utilidades', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'anio',
            [
                'label'=>'Valor',
                'attribute'=>'valor_uti',
                'value'=> function($model){
                     return number_format($model->valor_uti, 2, '.', ',');
                } ,
            ],
            'valor_uti_empleado',
            [
                'label'=>'($)Valor',
                'attribute'=>'valor_uti_empleado',
                'value'=> function($model){
                  return number_format(($model->valor_uti_empleado/($model->valor_uti_empleado +$model->valor_uti_carga))*$model->valor_uti, 2, '.', ',');
                } ,
            ],
            'valor_uti_carga',
            [
                'label'=>'($)Valor',
                'attribute'=>'valor_uti_carga',
                'value'=> function($model){
                return number_format(($model->valor_uti_carga/($model->valor_uti_empleado + $model->valor_uti_carga))*$model->valor_uti, 2, '.', ',');
                } ,
            ],
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){  
                     return $model->estado == 'P'? 'Liquidado':'Generado';  
                } ,
            ],
            'fecha',
            ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{liquidar}&nbsp{procesar}&nbsp{delete}',
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
                                'title' => 'Anular Permiso',
                                'data' => [
                                    'confirm' => 'Está seguro que desea anular el siguiente registro?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                        'liquidar' => function($url, $model){
                            return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, [
                                'title' => 'Liquidar',
                                'data' => [
                                    'confirm' => 'Está seguro que desea continuar?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                        'procesar' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                            'title' => 'Liquidar',
                            'data' => [
                                'confirm' => 'Está seguro que desea continuar?',
                                'method' => 'post',
                            ],
                        ]);
                        }, 
                      ],
                      'urlCreator'=>function($action,$data){
                          
                            if($action=='view'){  
                                return ['utilidades/view','id'=>$data->anio];
                            }
                            if($action == 'update'){   
                                return ['utilidades/update','id'=>$data->anio];
                            }
                            if($action == 'delete'){   
                                return ['utilidades/delete','id'=> $data->anio ];
                            }
                            if($action == 'liquidar'){
                                return ['utilidades/liquidar','id'=> $data->anio];
                            }
                            if($action == 'procesar'){
                                return ['utilidades/procesar','id'=> $data->anio];
                            }
                     }
                     
             ],
        ],
    ]); ?>
</div>
