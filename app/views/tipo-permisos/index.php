<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysRrhhPermisosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de  Permisos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-permisos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Permisos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_permiso',
            'permiso',
            //'id_sys_empresa',
             [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){
                
                return $model->estado == 'A'? 'Activo':'Inactivo';
                
                } ,
             ],
             //'transaccion_usuario',
            //'descuento',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
