<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAdmCargosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cargos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-adm-cargos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Cargo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_sys_adm_cargo',
            'cargo',
           // 'id_sys_adm_departamento',
            
            [
                'label'=>'Departamento',
                'attribute'=>'departamento',
                'value'=> function($model){
                return   utf8_encode( $model->sysAdmDepartamento->departamento);
                
                } ,
             ],
             [
                 'label'=>'Mando',
                 'attribute'=>'mando',
                 'value'=> function($model){
                 return   utf8_encode($model->sysAdmMando->mando);
                 
                 } ,
                 ],
            
           // 'id_sys_adm_mando',
            //'id_sys_empresa',
           
            [
                'label'=>'Horas/Extras',
                'attribute'=>'reg_horas_extras',
                'value'=> function($model){
                return   $model->reg_horas_extras =='S'? 'SI': 'No';
                
                } ,
             ],
             [
                 'label'=>'Entra/Salida',
                 'attribute'=>'reg_ent_salida',
                 'value'=> function($model){
                 return   $model->reg_ent_salida =='S'? 'SI': 'No';
                 
                 } ,
             ],
         
            //'id_sys_adm_departamento',
            //'id_sys_adm_mando',
           // 'estado',
            [
                'label'=>'Estado',
                'attribute'=>'estadp',
                'value'=> function($model){
                return   $model->estado =='A'? 'Activo': 'Inactivo';
                
                } ,
             ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
