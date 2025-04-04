<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhConceptosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conceptos de Nómina';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-conceptos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Concepto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_concepto',
            'concepto',
            [
                'label'=>'Tipo',
                'attribute'=>'tipo',
                'filter'=> ['I'=> 'Ingreso', 'E'=> 'Egreso'],
                'value'=> function($model){
                
                return $model->tipo == 'I'? 'Ingreso':'Egreso';
                
                } ,
                ],
                
                [
                    'label'=>'Pago',
                    'attribute'=>'pago',
                    'filter'=> ['1'=> 'Quincenal', '2'=> 'Mensual', '90'=> 'Proviciones', '70'=> 'Dec. Tercero', '71'=> 'Dec.Cuarto'],
                    'value'=> function($model){
                     
                     $pago = '';
                    
                     if ($model->pago == '1'){
                         $pago = 'Quincenal';
                     }
                     elseif ($model->pago == '2')
                     {
                        $pago = 'Mensual';
                     }
                     elseif ($model->pago == '90')
                     {
                         $pago = 'Proviciones';
                     }
                     elseif ($model->pago == '70')
                     {
                         $pago = 'Dec.Tercero';
                     }
                     elseif ($model->pago == '71')
                     {
                         $pago = 'Dec. Cuarto';
                     }
                     return $pago;
                    
                    } ,
               ],
                
               [
                   'label'=>'Estado',
                   'attribute'=>'estado',
                   'filter'=> ['A'=> 'Activo', 'I'=> 'Inactivo'],
                   'value'=> function($model){
                   
                   return $model->estado == 'A'? 'Activo':'Inactivo';
                   
                   } ,
               ],
          
           // 'imprime',
            //'orden',
            //'estado',
            //'agrupa',
            //'id_sys_empresa',
            //'aporta_iess',
            //'aporta_renta',
            //'transaccion_usuario',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
