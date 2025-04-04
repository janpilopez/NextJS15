<?php

use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhEmpleados;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysMedCertificadoMedicoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Certificado Médicos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-certficado-medico-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Certificado', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label'=>'Area',
                'attribute'=>'area',
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                
                        $empleado     = SysRrhhEmpleados::find()->Where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                        $cargo        = SysAdmCargos::find()->Where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])->one();
                        $departamento = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        $area         = SysAdmAreas::find()->Where(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                        
                        if($area):
                             return $area->area;
                        else:
                            return "s/n";
                        endif;
                
                },
                'contentOptions'=>['style'=>'width: 20%;']
             ],
            'id_sys_rrhh_cedula',
             [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                        $empleado        = SysRrhhEmpleados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                        return $empleado->nombres;
                    } ,
            ],
            [
                'label'=>'Entidad Emisora',
                'attribute'=>'entidad_emisora',
                'filter'=>  ['I'=> 'IESS', 'M'=>'MSP', 'P'=> 'PARTICULAR', 'O'=> 'OTROS', 'E'=>'PESPESCA'],
                'value'=> function($model){
                
                   $entidad = 'OTROS';
                   
                   if($model->entidad_emisora == 'I'):
                   
                       $entidad = 'IESS';
                      
                   elseif ($model->entidad_emisora == 'M'):
                   
                      $entidad = 'MSP';
                   
                   elseif ($model->entidad_emisora == 'P'):
                   
                      $entidad = 'PARTICULAR'; 

                   elseif ($model->entidad_emisora == 'E'):
                   
                      $entidad = 'PESPESCA'; 
                     
                   
                   endif;
                   
                   return $entidad;
                
                } ,
            ],
            
            [
                'label'=>'Tipo',
                'attribute'=>'tipo',
                'filter'=>  ['D'=> 'DIAS', 'H'=>'HORAS'],
                'value'=> function($model){
                    return $model->tipo == 'D' ? 'DIAS': 'HORAS';
                } ,
            ],
            [
                'label'=>'Tipo Ausentismo',
                'attribute'=>'tipo_ausentismo',
                'filter'=>  ['E'=> 'ENFERMEDAD', 'A'=>'ACCIDENTE'],
                'value'=> function($model){
                     return $model->tipo_ausentismo == 'E' ? 'ENFERMEDAD': 'ACCIDENTE';
                } ,
            ],
            [
                'label'=>'Inicio',
                'attribute'=>'fecha_ini',
                'filter' => DatePicker::widget([
                    
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_fin',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>['class'=>'form-control input-sm']
                ]),
                
                'format' => 'raw',
                'value'=> function($model){
                    return date('Y-m-d H:i', strtotime($model->fecha_ini));
                    
                } ,
             ],
             [
                 'label'=>'Fin',
                 'filter' => DatePicker::widget([
                     
                     'language' => 'es',
                     'model' => $searchModel,
                     'attribute' => 'fecha_fin',
                     'dateFormat' => 'yyyy-MM-dd',
                     'options'=>['class'=>'form-control input-sm']
                 ]),
                 'attribute'=>'fecha_fin',
                 'value'=> function($model){
                    return date('Y-m-d H:i', strtotime($model->fecha_fin));
                 } ,
             ],
            
            //'diagnostico',
           
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_actualizacion',
            //'fecha_actualizacion',
            //'anulado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
