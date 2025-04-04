<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysAdmAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Listado';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-areas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id_sys_adm_area',
            'cedula',
            'nombreProveedor',
            [
                'label'=>'Nivel Riesgo',
                'attribute'=>'nivel_riesgo',
                'filter'=> [1 => 'Bajo', 2 =>'Medio', 3=>'Alto'],
                'value'=> function($model){
                
                    if($model->nivel_riesgo == 1){
                        return 'Bajo';
                    }else if($model->nivel_riesgo == 2){
                        return 'Medio';
                    }else{
                        return 'Alto';
                    }
                
                
                } ,
            ],
           // 'id_sys_empresa',
          //  'estado',

          ['class' => 'yii\grid\ActionColumn',
          'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}',
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
              
            ],
              
            'urlCreator'=>function($action,$data){
                  if($action=='view'){
                      
                      return ['proveedores/view','id'=>$data->idProveedor];
                  }
                  
                  if($action == 'update'){
                      return ['proveedores/update','id'=>$data->idProveedor];
                  }
            }
            ],
        ],
    ]); ?>


</div>
