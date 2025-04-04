<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhCuadrillasJornadasMovSearch*/
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agendamiento Laboral';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-cuadrillas-jornadas-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Agendamiento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_empresa',
           [
                'label'=>'Grupo',
                'attribute'=>'cuadrilla',
                'format' => 'raw',
                'value'=> function($model){
                  return  $model->sysRrhhCuadrillas->cuadrilla;
                
                } ,
                ],
              
            'semana',
            'fecha_inicio',
            'fecha_fin',
            //'id_sys_rrhh_cuadrillas',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{pdf}&nbsp{update}&nbsp{delete}',
                'buttons'=>[
                    
                    'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => 'Ver',
             
                    ]);
                    },
                    'update' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => 'Ajustar Agenda',
                        
                    ]);
                    },
                    'pdf' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-save-file"></span>', $url, [
                        'title' => 'Descargar',
                        
                    ]);
                    },
                    'delete' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => 'Anular Agendamiento',
                        'data' => [
                            'confirm' => 'Está seguro que desea anular el siguiente registro?',
                            'method' => 'post',
                        ],
                        
                    ]);
                    },
                    ],
                    'urlCreator'=>function($action,$data){
                    if($action=='view'){
                        return ['agendamiento/view','id'=>$data->id_sys_rrhh_cuadrillas_jornadas_cab];
                    }
                    
                    if($action =='pdf'){
                        return ['agendamiento/verpdf','id'=>$data->id_sys_rrhh_cuadrillas_jornadas_cab, ['target'=>'_blank']];
                    }
           
                    if($action == 'update'){
                        return ['agendamiento/ajustaragenda','id'=>$data->id_sys_rrhh_cuadrillas_jornadas_cab];
                     }
                   
                    if($action =='delete'){
                        
                          return ['agendamiento/delete','id'=>$data->id_sys_rrhh_cuadrillas_jornadas_cab,];   
                    }
                }
            ],
        ],
    ]); ?>


</div>
