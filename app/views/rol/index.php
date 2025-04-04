<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysRrhhEmpleadosRolCabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Periodos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 



?>



<div class="sys-rrhh-empleados-rol-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Periodo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'anio',
         
            [
                'label'=>'mes',
                'attribute'=>'mes',
                'value'=> function($model){
                     $meses = Yii::$app->params['meses'];
                     return  $meses[$model->mes];
                } ],
            'periodo',
            
            //'fecha_registro',
           // 'estado',
            [
                'label'=>'estado',
                'attribute'=>'estado',
               'value'=> function($model){
                   return  $model->estado == 'Q' ? 'Generado': 'Liquidado';
                } ],
            //'id_sys_empresa',
            //'transaccion_usuario',
            'fecha_ini_liq',
            'fecha_fin_liq',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{delete}&nbsp{procesar}&nbsp{liquidar}',
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
                        'title' => 'Anular Periodo',
                        'data' => [
                            'confirm' => 'Está seguro que desea anular el siguiente registro?',
                            'method' => 'post',
                        ],
                        
                    ]);
                    },
                    'procesar' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                        'title' => 'Procesar Periodo',
                        'data' => [
                            'confirm' => 'Está seguro que quiere procesar el rol?',
                            'method' => 'post',
                        ],
                        
                    ]);
                    },
                    
                    
                    'liquidar' => function($url, $model){
                    
                    return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url, [
                        'title' => 'Liquidar',
                        
                    ]);
                    },
                    
                    
                    
                    ],
                    'urlCreator'=>function($action,$data){
                    if($action=='view'){
                        
                        return ['rol/view','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
              
                    }
                    
                    if($action == 'update'){
                        
                        return ['rol/update','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                      
                    }
                    
                    if($action =='delete'){
                        
                        return ['rol/delete','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                        
                    }
                    if($action =='procesar'){
                        
                        return ['rol/procesar','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                        
                    }
                    
                    if($action =='liquidar'){
                        
                        return ['rol/liquidar','anio'=>$data->anio, 'mes'=> $data->mes, 'periodo'=> $data->periodo, 'id_sys_empresa'=> $data->id_sys_empresa];
                        
                    }
}
                    ],
        ],
    ]); ?>


</div>
