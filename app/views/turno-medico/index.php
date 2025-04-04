<?php

use app\assets\TurnoMedicoAsset;
use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmUsuariosDep;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use app\models\SysRrhhEmpleados;
use app\models\SysMedTipoMotivo;
use yii\jui\DatePicker;
use app\models\SysMedConsultaMedica;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
TurnoMedicoAsset::register($this);

$this->render('../_alertFLOTADOR');
$this->title = 'Turnos';
$this->params['breadcrumbs'][] = $this->title;
$url = Yii::$app->urlManager->createUrl(['turno-medico']);
$inlineScript = "var url='$url'";
$this->registerJs($inlineScript, View::POS_HEAD);


$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];
$departamentos =[];


if($userdeparta):

    if(trim($userdeparta->area) != ''):
        $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
    else:
         $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
    endif;
    
    if(trim($userdeparta->departamento) != ''):
        $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
    else:
        $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
    endif;

endif;

?>

<style>
.clockdate {
    text-align:right;
}
.clockdate-wrapper {
    background-color: #333;
    padding:15px;
    max-width:300px;
    width:100%;
    text-align:center;
    border-radius:5px;
    margin-left:auto;
    margin-top:1%;
}
#clock{
    background-color:#333;
    font-family: sans-serif;
    font-size:20px;
    text-shadow:0px 0px 1px #fff;
    color:#fff;
}
#clock span {
    color:#888;
    text-shadow:0px 0px 1px #333;
    font-size:30px;
    position:relative;
    top:-27px;
    left:-10px;
}
#date {
    letter-spacing:10px;
    font-size:14px;
    font-family:arial,sans-serif;
    color:#fff;
}
.titulo {

 font-size:250px;
 font-weight: bold;
}
</style>


<div class="sys-med-turno-medico-index">

    <h1><?= Html::encode($this->title) ?></h1>

    
        <div class="clockdate" id="clockdate">
            <div class="clockdate-wrapper">
                <div id="clock"></div>
            </div>
        </div>


    <p>
        <?= Html::a('Registrar Turno', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'numero',
            [
                'label'=>'Area',
                'attribute'=>'area',
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                    
                        $empleado        = SysRrhhEmpleados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                        $cargo           = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])->one();
                        $departamento    = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        $area            = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                
                if($area):
                    return $area->area;
                else:
                    return "s/n";
                endif;
                
                },
                'contentOptions'=>['style'=>'width: 20%;']
                ],
                
           [
              'label'=>'Cédula',
              'attribute'=>'id_sys_rrhh_cedula',
              'contentOptions'=>['style'=>'width: 8%;']
            ],
           
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                    
                     $empleado        = SysRrhhEmpleados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                     return $empleado->nombres;
              } ,
            ],
            
            [
              'label'=>'Motivo',
               'attribute'=>'id_sys_med_tipo_motivo',
               'filter'=> ArrayHelper::map(SysMedTipoMotivo::find()->all(), 'id', 'tipo'),
               'value'=> function($model){
                   
                        $motivo = SysMedTipoMotivo::find()->where(['id'=> $model->id_sys_med_tipo_motivo])->one();
                        
                        if($motivo):
                            return $motivo->tipo;
                        else:
                            return  "S/D";
                        endif;
                } 
            ],
            [
              'attribute' => 'fecha',
              'filter' => DatePicker::widget([
                        'language' => 'es',
                        'model' => $searchModel,
                        'attribute' => 'fecha',
                        'dateFormat' => 'yyyy-MM-dd',
                        'options'=>['class'=>'form-control input-sm']
                    ]
                        ),
                    'format' => 'raw',
                    
            ],
            [
                'label'=>'Inicio Turno',
                'attribute'=>'ini_atencion',
                'value'=> function($model){
                   
                    if($model->ini_atencion != null):
                          
                        return date('H:i:s', strtotime($model->ini_atencion));
                    
                    else:
                    
                       return "00:00:00";
                    
                    endif;
                
                    
               
                },
                'contentOptions'=>['style'=>'width: 5%;']
            ],
            
            [
                'label'=>'Inicio Consulta',
                'attribute'=>'ini_atencion',
                'value'=> function($model){
                
                      $consulta = SysMedConsultaMedica::find()->where(['id_sys_med_turno_medico'=> $model->id])->one();
                      if($consulta):
                         return date('H:i:s', strtotime($consulta->fecha_toma_datos));
                      endif;
                      return  "00:00:00";
            
                
                },
                'contentOptions'=>['style'=>'width: 5%;']
            ],
                
            [
                'label'=>'Fin Atención',
                'attribute'=>'fin_atencion',
                'value'=> function($model){
                   
                    if($model->fin_atencion != null):
                    
                        return date('H:i:s', strtotime($model->fin_atencion));
                    
                    else:
                    
                       return "00:00:00";
                    
                    endif;
                
                   
                
                }
                ,'contentOptions'=>['style'=>'width: 5%;']
            ],
            [
                'label'=>'Atendido',
                'attribute'=>'atendido',
                'filter'=> ['1' => 'Atendido','0' => 'Sin Atender'],
                'value'=> function($model){
                
                if($model->atendido == true):
                    
                        return Html::a('Antendido', [''], [
                            'class' => 'btn btn-xs btn-success btn-block',
                        ]);
                    else:
                    
                        return Html::a('Sin Atender', [''], [
                            'class' => 'btn btn-xs btn-warning btn-block',
                        ]);
                    
                    endif;
                
                },
                'format' => 'raw',
                
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
