<?php

use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleados;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysMedFichaMedicaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Ficha Médica';
$this->params['breadcrumbs'][] = $this->title;


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
<div class="sys-med-ficha-medica-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
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
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                
                    $empleado        = SysRrhhEmpleados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    return $empleado->nombres;
                    
                   } ,
                ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
