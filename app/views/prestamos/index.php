<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysRrhhEmpleados;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhPrestamosCabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Préstamos Empresa';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-prestamos-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Préstamo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id_sys_rrhh_prestamos_cab',
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'format' => 'raw',
                'value'=> function($model){
                
                   return $model->empleado->nombres; 
                   
                  }
                ],
            'fecha',
            'comentario',
            'valor',
            
          
            //'cuotas',
            //'anio_ini',
            //'mes_ini',
            //'periodo_rol',
            //'nperiodo',
            [ 'label'=>'autorizacion',
                'attribute'=>'autorizacion',
                'filter'=> ['A' => 'Autorizado', 'P' =>'Pendiente', 'N'=>'No Autorizado'],
                'value'=> function($model){
                    
                   if($model->autorizacion == 'A'):
                      
                       return 'Autorizado';
                   
                   elseif($model->autorizacion == 'P'):
                   
                      return 'Pendiente ';
                   
                   else:
                   
                      return 'No Autorizado';
                   
                   endif;
               
                } ],
            //'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
