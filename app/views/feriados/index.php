<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel  app\models\search\SysRrhhFeriadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Feriados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-feriados-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Feriado', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id_sys_rrhh_feriado',
            
           // 'fecha',
            
            [
                'attribute' => 'fecha',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha',
                  
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'],
                        'options'=>['class'=>'form-control input-sm']
                ]
                    ),
                'format' => 'raw',
                ],
            
            
            
            //'feriado',
            
            [
                //'label'=>'Nacional',
                'attribute'=>'feriado',
                'value'=> function($model){
                
                 return  $model->feriado;
                
                } 
                ],
            
            //'nacional',
            [
                'label'=>'Nacional',
                'attribute'=>'nacional',
                'value'=> function($model){
                
                return $model->nacional == 'S'? 'Si':'No';
                
                } ,
                ],
           // 'id_sys_provincia',
            //'id_sys_canton',
            //'id_sys_empresa',
            //'transaccion_usuario',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
