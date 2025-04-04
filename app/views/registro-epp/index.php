<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

$this->title = 'Listado';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-ssoo-registro-epp-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Registrar EPP Empleado', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nombre',
            // 'estado',
            // 'vida_util',
            'id_sys_rrhh_cedula',
            [
                'attribute' => 'fecha_registro',
                'value' => function ($model) {
                    if ($model->fecha_registro) {
                        return Yii::$app->formatter->asDate($model->fecha_registro, 'php:Y-m-d'); // Formato personalizado
                    }
                    return "";
                },
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_registro',
                    'options' => ['class' => 'form-control input-sm text-center'],
                ]),
            ],

            [
                'attribute' => 'username_empleado',
                'label' => 'Nombre empleado',  // Puedes cambiar el nombre de la columna si es necesario
                'value' => function ($model) {
                    return $model ? $model->empleado->nombres : "";
                },
                // 'filter' => Html::activeTextInput($searchModel, 'username_empleado', [
                //     'class' => 'form-control',
                //     'placeholder' => 'Buscar por username...',
                // ]),  // Agrega un filtro si es necesario para la búsqueda por username
                'format' => 'raw',
            ],
            
          ['class' => 'yii\grid\ActionColumn',
          'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}',
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
              
            ],
              
            'urlCreator'=>function($action,$data){
                  if($action=='view'){
                      
                      return ['registro-epp/view','id'=>$data->id_sys_ssoo_registro_entrega_epp];
                  }
                  
                  if($action == 'update'){
                      return ['registro-epp/update','id'=>$data->id_sys_ssoo_registro_entrega_epp];
                  }
            }

            ],
        
        ],

    ]); ?>


</div>
