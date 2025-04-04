<?php

use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\grid\GridView;
$this->render('../_alertFLOTADOR'); 
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhEmpleadosRolAjusteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rol Ajuste';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-empleados-rol-ajuste-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Ajuste', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_rrhh_empleados_rol_ajuste',
            'anio',
            [
                'label'=>'mes',
                'attribute'=>'mes',
                'format' => 'raw',
                'value'=> function($model){
                
                  return Yii::$app->params['meses'][$model->mes];
                
                  
                }
                
                ],
      
            
           
            [
                'label'=>'Periodo',
                'attribute'=>'periodo',
                'format' => 'raw',
                'value'=> function($model){
                    
                   return Yii::$app->params['periodos'][$model->periodo];
                }
                
            ],
            'id_sys_rrhh_concepto',
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'Nombres',
                'format' => 'raw',
                'value'=> function($model){
                $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
                return $nombres;
                
                }
                
                ],
            'valor',
          //  'fecha_registro',
         
            //'transaccion_usuario',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
