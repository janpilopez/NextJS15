<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysRrhhEmpleados;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Finiquitos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-finiquito-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_finiquito_cab',
            'id_sys_rrhh_cedula',
          
            [
                'label'=>'Nombres ',
                'attribute'=>'nombres',
                
                'format' => 'raw',
                'value'=> function($model){
                           
                      $empleados = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                     return $empleados->nombres;
                }
                ],
            //'id_sys_empresa',
            'fecha_registro',
            //'estado',
            //'fecha_creacion',
            //'usuario_creacion',
            //'fecha_actualizacion',
            //'usuario_actualizacion',
            //'anulada:boolean',
            //'comentario',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
