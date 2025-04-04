<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

$this->title = 'Listado Epp por vida útil';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>

<div class="sys-ssoo-registro-epp-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id_sys_ssoo_epp',
                'label' => 'Equipo',  // Puedes cambiar el nombre de la columna si es necesario
                'value' => function ($model) {
                    return $model->epp->nombre;
                },
                'filter' => Html::activeTextInput($searchModel, 'nombreEquipo', [
                    'class' => 'form-control',
                    'placeholder' => 'Buscar por nombre del equipo...',
                ]),
                'format' => 'raw',
            ],
            [
                'attribute' => 'id_sys_ssoo_epp',
                'label' => 'Empleado',  // Puedes cambiar el nombre de la columna si es necesario
                'value' => function ($model) {
                    return $model->registroepp->empleado->id_sys_rrhh_cedula . ' ' . $model->registroepp->empleado->nombres;
                },
                'filter' => Html::activeTextInput($searchModel, 'nombreEmpleado', [
                    'class' => 'form-control',
                    'placeholder' => 'Buscar por nombre del equipo...',
                ]),
                'format' => 'raw',
            ],
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
                'attribute' => 'fecha_vencimiento',
                'value' => function ($model) {
                    if ($model->fecha_vencimiento) {
                        return Yii::$app->formatter->asDate($model->fecha_vencimiento, 'php:Y-m-d'); // Formato personalizado
                    }
                    return "";
                },
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_vencimiento',
                    'options' => ['class' => 'form-control input-sm text-center'],
                ]),
            ],
            [
                'attribute' => 'id_empleado_registro',
                'label' => 'Registrado por',  // Puedes cambiar el nombre de la columna si es necesario
                'value' => function ($model) {
                    return $model->empleadoRegistro->nombres ? $model->empleadoRegistro->nombres : $model->username_empleado_registro;
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Vida Util Inicial | Estimada',  // Puedes cambiar el nombre de la columna si es necesario
                'value' => function ($model) {
                    return $model->epp->vida_util ? 
                        $model->epp->vida_util . " | " . $model->vida_util
                        : " ";
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center col-md-1 word-break', // Asigna la clase CSS para centrar el contenido
                ],
            ],
            // [
            //     'label' => 'Vida Útil', // Título de la columna
            //     'attribute' => 'vida_util', // Propiedad o atributo que deseas mostrar
            //     'format' => 'raw', // Formato de la columna (raw para no modificar la salida)
            //     'contentOptions' => [
            //         'style' => 'text-align: center;', // Centra el contenido en la celda
            //     ],
            // ],
            [
                'label' => 'Días de Uso', // Título de la columna
                'value' => function ($model) {
                    // Verifica si la fecha de vencimiento está definida
                    return $model->diasTranscurridos;
                },
                'format' => 'raw', // Formato de la columna
                'contentOptions' => [
                    'class' => 'text-center', // Asigna la clase CSS para centrar el contenido
                ],

            ],
            [
                'label' => 'Días restantes', // Título de la columna
                'value' => function ($model) {
                    // Obtenemos los días restantes
                    $diasRestantes = $model->vida_util - $model->diasTranscurridos;

                    // Asignamos la clase CSS según el valor de los días restantes
                    if ($diasRestantes > 15) {
                        // Verde
                        return "<span style='background-color: green; color: white; padding: 5px;'>$diasRestantes</span>";
                    } elseif ($diasRestantes <= 15 && $diasRestantes > 5) {
                        // Amarillo
                        return "<span style='background-color: yellow; color: black; padding: 5px;'>$diasRestantes</span>";
                    } else {
                        // Rojo
                        return "<span style='background-color: red; color: white; padding: 5px;'>$diasRestantes</span>";
                    }
                },
                'format' => 'raw', // Formato de la columna
                'contentOptions' => [
                    'class' => 'text-center', // Asigna la clase CSS para centrar el contenido
                ],
                'filter' => '<div class="row">
                        <div class="col-md-6">' .
                            Html::activeTextInput($searchModel, 'diasRestantesDesde', [
                                'class' => 'form-control',
                                'placeholder' => 'Desde',
                            ]) .
                        '</div>
                        <div class="col-md-6">' .
                            Html::activeTextInput($searchModel, 'diasRestantesHasta', [
                                'class' => 'form-control',
                                'placeholder' => 'Hasta',
                            ]) .
                        '</div>
                     </div>',
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
                
                return Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', $url, [
                    'title' => 'Agregar',
                    
                ]);
                },
                
              ],
                
              'urlCreator'=>function($action,$data){
                    if($action=='view'){
                        
                        return ['registro-epp/viewinspeccion','id'=>$data->sys_ssoo_registro_entrega_detalle_id];
                    }
                    
                    if($action == 'update'){
                        return ['registro-epp/inspeccion','id'=>$data->sys_ssoo_registro_entrega_detalle_id];
                    }
              }
  
              ],

        ],

    ]); ?>


</div>