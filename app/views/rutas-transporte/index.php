<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAdmRutasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rutas de Transporte';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-rutas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Ruta', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_adm_ruta',
            'ruta',
           // 'id_sys_empresa',
           // 'estado',
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){
                
                return $model->estado == 'A'? 'Activo':'Inactivo';
                
                } ,
                ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
