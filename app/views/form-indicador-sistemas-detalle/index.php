<?php

use app\models\SysAdmDepartamentos;
use app\models\SysEncabezadoIndicador;
use app\models\SysIndicadores;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysAdmAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$encabezado = SysEncabezadoIndicador::find()->where(['id_encabezado_indicador'=>$id_encabezado_indicador])->one();
$tipoIndicador = SysIndicadores::find()->where(['id_indicador'=>$encabezado['tipo_indicador']])->one();

$this->title = 'Detalle Indicador '.ucwords(strtolower($tipoIndicador['nombre_indicador']));
$this->params['breadcrumbs'][] = ['label' => 'Formulario Indicador Sistemas', 'url' => ['form-indicador-sistemas/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-areas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Datos Indicador '.ucwords(strtolower($tipoIndicador['nombre_indicador'])), ['create','id_encabezado_indicador'=>$id_encabezado_indicador], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label'=>'Tipo Indicador',
                'attribute'=>'tipo_indicador',
                'format' => 'raw',
                'value'=> function($model){
                    $indicador = SysIndicadores::find()->Where(['id_indicador'=> $model->tipo_indicador])->one();
                    
                    return $indicador->nombre_indicador ;
                }
            ],
            [
                'label'=>'Departamento',
                'attribute'=>'departamental',
                'format' => 'raw',
                'value'=> function($model){
                    $departamento = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $model->departamental])->one();
                    
                    return $departamento->departamento ;
                }
            ],
            [
                'label'=>'Ubicación Impresora',
                'attribute'=>'imp_departamento',
                'format' => 'raw',
                'value'=> function($model){
                  
                    $departamento = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $model->imp_departamento])->one();
                    
                    return $departamento->departamento ;
                }
            ],
            [
                'attribute' => 'fecha',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha',
                  
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'],
                        'options'=>['class'=>'form-control input-sm']
                ]
                    ),
                'format' => 'raw',
            ],
            
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{update}',
                'buttons'=>[

                    'view' => function($url, $model){
                            
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Detalle',
                                    
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
                                
                        return ['form-indicador-sistemas-detalle/view','id_cuerpo_indicador'=>$data->id_cuerpo_indicador];
                                
                    }
                    if($action == 'update'){
                                
                        return ['form-indicador-sistemas-detalle/update','id_cuerpo_indicador'=>$data->id_cuerpo_indicador];
                        
                    }
                        
                }
            ],
        ],
    ]); ?>


</div>
