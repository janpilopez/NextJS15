<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhCausaSalidaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Causas Salidas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-causa-salida-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Causa Salida', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_causa_salida',
            
            [
                'label'=>'Descripcion',
                'attribute'=>'descripcion',
                'value'=> function($model){
                
                   return  $model->descripcion;
                
                } ,
            ],
    
            
           // 'id_sys_empresa',
           // 'indemnizacion',
            //'bonificacion',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
