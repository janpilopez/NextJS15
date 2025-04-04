<?php

use app\models\SysAdmDepartamentos;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhComedorVisitasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comedor Visitas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-comedor-visitas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Credencial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codigo',
            [
                'label'=>'Departamento',
                'attribute'=>'id_sys_adm_departamento',
                'filter'=> ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=>'A'])->andwhere(['id_sys_empresa'=> '001'])->orderBy('departamento')->all(), 'id_sys_adm_departamento', 'departamento') ,
                'format' => 'raw',
                'value'=> function($model){
                
                   $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $model->id_sys_adm_departamento])->one();
                
                   return $departamento->departamento ;
                }
            ],
            [
                'label'=>'tipo_visita',
                'attribute'=>'tipo_visita',
                'format' => 'raw',
                'value'=> function($model){
                
                     return $model->tipo_visita == 'C' ? 'Cerrada' : 'Abierta'; 
                
                }
           ],
           [
               'label'=>'desayuno',
               'attribute'=>'desayuno',
               'format' => 'raw',
               'value'=> function($model){
               
                   return $model->desayuno == 'N' ? 'No' : 'Si';
               
               }
           ],
           [
               'label'=>'almuerzo',
               'attribute'=>'almuerzo',
               'format' => 'raw',
               'value'=> function($model){
               
                    return $model->almuerzo == 'N' ? 'No' : 'Si';
               
               }
           ],
           [
               'label'=>'merienda',
               'attribute'=>'merienda',
               'format' => 'raw',
               'value'=> function($model){
               
                     return $model->merienda == 'N' ? 'No' : 'Si';
               
               }
          ],
          [
              'label'=>'estado',
              'attribute'=>'estado',
              'format' => 'raw',
              'value'=> function($model){
              
              return $model->estado == 'A' ? 'Activa' : 'Inactiva';
              
              }
              ],
    
            //'merienda',
            // 'id_sys_rrhh_comedor_visita',
            //'codigo',
            //'id_sys_adm_departamento',
            //'id_sys_empresa',
            //'estado',

           // ['class' => 'yii\grid\ActionColumn'],
           
              ['class' => 'yii\grid\ActionColumn',
                  'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{update}&nbsp{delete}&nbsp{credencial}',
                  'buttons'=>[
                      
                      'view' => function ($url, $model) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                          'title' => 'Ver',
                          
                      ]);
                      },
                      'update' => function($url, $model){
                      
                      return Html::a('<span class="glyphicon glyphicon glyphicon-pencil"></span>', $url, [
                          'title' => 'Actualizar',
                          
                      ]);
                      },
                      
                      
                      'delete' => function($url, $model){
                      
                      return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                          'title' => 'Eliminar',
                          'data' => [
                              'confirm' => 'Está seguro que desea anular el siguiente registro?',
                              'method' => 'post',
                          ],
                          
                      ]);
                      },
                      'credencial' => function($url, $model){
                      
                      return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                          'title' => 'Imprimir Solicitud'
                          
                      ]);
                      },
                      
                      
                      ],
                      'urlCreator'=>function($action,$data){
                      if($action=='view'){
                          
                          return ['comedor-visitas/view','id_sys_rrhh_comedor_visita'=>$data->id_sys_rrhh_comedor_visita, 'id_sys_empresa'=> $data->id_sys_empresa];
                      }
                      
                      if($action == 'update'){
                          return ['comedor-visitas/update','id_sys_rrhh_comedor_visita'=>$data->id_sys_rrhh_comedor_visita, 'id_sys_empresa'=> $data->id_sys_empresa];
                      }
                      
                      if($action =='delete'){
                          
                          return ['comedor-visitas/delete','id_sys_rrhh_comedor_visita'=> $data->id_sys_rrhh_comedor_visita, 'id_sys_empresa'=> $data->id_sys_empresa ];
                      }
                      if($action =='credencial'){
                          
                          return ['comedor-visitas/imprimecredencial','id_sys_rrhh_comedor_visita'=> $data->id_sys_rrhh_comedor_visita, 'id_sys_empresa'=> $data->id_sys_empresa ];
                      }
                      }
                      ],
              
              
              
        ],
    ]); ?>


</div>
