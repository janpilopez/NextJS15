<?php

use app\models\SysAdmDepartamentos;
use app\models\SysIndicadores;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysAdmAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Formulario Indicador Sistemas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-areas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Datos Indicador', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute'=>'id_sys_adm_departamento',
                'format' => 'raw',
                'value'=> function($model){
                    $departamento = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $model->id_sys_adm_departamento])->one();
                    
                    return $departamento->departamento ;
                }
            ],
            [
                'label'=>'Frecuencia',
                'attribute'=>'frecuencia',
                'format' => 'raw',
                'value'=> function($model){
                  
                    if ($model->frecuencia== 'M') {
                        return 'MENSUAL';
                    }else if($model->frecuencia == 'T'){
                        return 'TRIMESTRAL';
                    }else if($model->frecuencia == 'S'){
                        return 'SEMESTRAL';
                    }else if($model->frecuencia == 'A'){
                        return 'ANUAL';
                    }
                }
            ],
            'meta',
            'efecto_medir',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'<div class="text-center" style="display:flex">{view}&nbsp{list}',
                'buttons'=>[

                    'view' => function($url, $model){
                            
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Datos',
                                    
                        ]);
                    },

                    'list' => function($url, $model){
                            
                        return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', $url, [
                            'title' => 'Detalle',
                                    
                        ]);
                    },
                            
                ],
                
                'urlCreator'=>function($action,$data){
                    if($action=='view'){
                                
                        return ['form-indicador-sistemas/view','id_encabezado_indicador'=>$data->id_encabezado_indicador];
                                
                    }

                    if($action=='list'){
                                
                        return ['form-indicador-sistemas-detalle/index','id_encabezado_indicador'=>$data->id_encabezado_indicador];
                                
                    }
                        
                }
            ],
        ],
    ]); ?>


</div>
