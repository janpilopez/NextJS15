<?php

use app\models\SysAdmAreas;
use app\models\SysRrhhEmpleados;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysSsooIncidenteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'SSOO Incidentes';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-ssoo-incidente-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Incidente', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'label'=>'Secuencial',
                'attribute'=>'secuencial',
                'value'=> function($model){
                   return str_pad($model->secuencial, 10, "0", STR_PAD_LEFT);
                } ,
            ],
            [
                'attribute' => 'fecha',
                'filter' => DatePicker::widget([
                    
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>['class'=>'form-control']
                ]),
                'value'=> function($model){
                   return date('Y-m-d', strtotime($model->fecha)); 
                },
                'format' => 'raw',
                
            ],
            [
                'label'=>'Area',
                'attribute'=>'id_sys_adm_area',
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                
                        $area = SysAdmAreas::find()->Where(['id_sys_adm_area'=> $model->id_sys_adm_area])->one();
                    
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
                    
                    $empleado = SysRrhhEmpleados::find()->Where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    return $empleado->nombres;
                    
                } ,
            ],
           // 'id_sys_med_consulta_medica',
            //'secuencial',
            //'codigo',
            //'turno',
            //'fecha',
            //'lugar',
            //'puesto_trabajo',
            //'lesion_corporal',
            //'danio_maquinaria',
            //'danio_instalaciones',
            //'danio_epp',
            //'observacion',
            //'descripcion_incidente',
            //'analisis_problema',
            //'correcion',
            //'accion_preventiva',
            //'notifica_incidente_nombre',
            //'notifica_incidente_cargo',
            //'anulado',
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_actualizacion',
            //'fecha_actualizacion',
            //'usuario_anulacion',
            //'fecha_anulacion',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
