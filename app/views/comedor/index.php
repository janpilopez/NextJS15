<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhComedoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Horario Comedor';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-comedor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_comedor',
            
            [
                'attribute'=>'alimento',
                'value'=> function($model){
                    return strtoupper($model->alimento);
                } ,
            ],
            [
                'attribute'=>'h_desde',
                'value'=> function($model){
                    return date('H:i', strtotime($model->h_desde));
                } ,
            ],
            [
                'attribute'=>'h_hasta',
                'value'=> function($model){
                    return date('H:i', strtotime($model->h_hasta));
                } ,
            ],
            [
                'attribute'=>'tiempo_descuento',
                'value'=> function($model){
                    return date('H:i', strtotime($model->tiempo_descuento));
                } ,
            ],
          //  'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
