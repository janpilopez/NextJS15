<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysRrhhEmpleados;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysAdmAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gastos Proyectados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-empleados-gastos-proyectados-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Gastos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id_sys_adm_area',
            //'id_gasto_proyectado',
            'id_sys_rrhh_cedula',
            [
                //'header' => 'Estado',
                'attribute'=> 'nombres',
                'value' => function ($model) {
                   $empleados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                
                return $empleados->nombres;
                },
                'format' => 'raw',
                
            ],
            'anio',
           // 'id_sys_empresa',
          //  'estado',

          ['class' => 'yii\grid\ActionColumn',
          'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{download}',
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
              'download' => function($url, $model){

                return Html::a('<span class="glyphicon glyphicon-save-file"></span>', $url,[
                    'title' => 'Descargar documento',
                ]);
              }
              
              
              ],
              'urlCreator'=>function($action,$data){
                if($action=='view'){
                    
                    return ['gastos-proyectados/view','id'=>$data->id_gasto_proyectado];
                }

                if($action == 'update'){
                    return ['gastos-proyectados/update','id'=>$data->id_gasto_proyectado];
                }

                if($action == 'download'){
                    return ['gastos-proyectados/download','id'=>$data->id_gasto_proyectado];
                }
    
              }
              ],
        ],
    ]); ?>


</div>
