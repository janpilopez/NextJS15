<?php

use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\SysAdmCargos;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhVacacionesSolicitud;
use app\models\SysAdmPeriodoVacaciones;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhEmpleadosPeriodoVacacionesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vacaciones';
$this->params['breadcrumbs'][] = $this->title;

$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
$areas = [];
$departamentos =[];

if($userdeparta):

    if(trim($userdeparta->area) != ''):
        $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
    else:
        $areas =  SysAdmUsuariosDep::find()->select('area')->asArray()->column();
    endif;
    
    if(trim($userdeparta->departamento) != ''):
         $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
    else:
         $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
    endif;

endif;

?>
<div class="sys-rrhh-empleados-periodo-vacaciones-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class = 'pull-right'>
        <?= Html::a('Registrar Solicitud', [ Url::to('/solicitud-vacaciones/create')], ['class' => 'btn btn-success', 'target'=> '_blank']) ?>
        <?= Html::a('Ver Agenda', [ Url::to('/vacaciones/vercalendario')], ['class' => 'btn btn-warning', 'target'=> '_blank']) ?>
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
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                   
                    $cargo        = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->empleado->id_sys_adm_cargo])->one();
                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                    $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                   
                    if($area):
                         return $area->area;
                    else:
                         return 's/n';
                    endif;
                }
            ],
            [
                'label'=>'Departamento',
                'attribute'=>'departamento',
                'filter'=>  ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_departamento'=> $departamentos])->all(), 'id_sys_adm_departamento', 'departamento'),
                'format' => 'raw',
                'value'=> function($model){
                    
                    $cargo        = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->empleado->id_sys_adm_cargo])->one();
                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                    return $departamento->departamento;
                
                    if($departamento):
                        $departamento->departamento;
                    else:
                        return 's/n';
                    endif;
                    
                    
                }
                
             ],
            'id_sys_rrhh_cedula',
            
            
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'format' => 'raw',
                'value'=> function($model){
                
                    return $model->empleado->nombres;
                
                }
            ],
            [
                'label'=>'Período',
                'attribute'=>'periodo',
                'filter'=> ArrayHelper::map(SysAdmPeriodoVacaciones::find()->where(['id_sys_empresa'=> '001'])->andWhere(['<=','anio_vac_hab', date('Y')])->all(), 'id_sys_adm_periodo_vacaciones', 'periodo'),
                'format' => 'raw',
                'value'=> function($model){
                
                    return $model->periodo->periodo;
                
                }
             ],
            //'id_sys_rrhh_empleados_periodo_vacaciones',
             'dias_disponibles',
             'dias_otorgados',
            
             [
                 'label'=>'Dias Pendientes',
                 'attribute'=>'Dias Pendientes',
                
                 'value'=> function($model){
                 
                  // return $model->estado == 'P'? 'Pendiente': 'Gozados';
                    
                    return $model->dias_disponibles - $model->dias_otorgados;
                 
                 }
              ],
             
             [
                 'label'=>'Estado Periodo',
                 'attribute'=>'estado',
                 'filter'=> ['P' => 'Pendiente','A' =>'Anticipadas','T' =>'Gozadas'],
                 'format' => 'raw',
                 'value'=> function($model){
                    
                    if($model->dias_disponibles <= 0 ){
                        return $model->estado == 'A'? 'Anticipadas':'Pendiente';
                    }else{
                        return $model->estado == 'P'? 'Pendiente': 'Gozadas';
                    }
                    
                    //return $condicion;
                 }
              ],


            //'id_sys_rrhh_cedula',
            //'id_sys_empresa',
            //'id_sys_adm_periodo_vacaciones',
            //'dias_laborados',
            //'valor',

           /* ['class' => 'yii\grid\ActionColumn',
            
            'template'=>'<div class="text-center" style="display:flex"> {view}',
            'buttons'=>[
                
                'view' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                    'title' => 'Ver',
                    
                ]);
                },
            
                ],
                'urlCreator'=>function($action,$data){
                if($action=='view'){
                    return ['view','id'=>$data->id_sys_rrhh_empleados_periodo_vacaciones];
                }
                
              }
            
            
            
            
        ]*/
       ]
    ]); ?>


</div>
