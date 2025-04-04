<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAdmDepartamentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Departamentos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-departamentos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Departamento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'yii\grid\SerialColumn'],
         //   'id_sys_adm_departamento',
           // 'departamento',
           
            [
                'label'=>'Departamento',
                'attribute'=>'departamento',
                'value'=> function($model){
                
                    return   $model->departamento;
                
                } ,
            ],

            //'rango_ip_inicio',
            //'rango_ip_fin',
            //'id_sys_empresa',
            //'id_sys_adm_area',
            //'color',
            [
                'label'=>'Siglas',
                'attribute'=>'siglas',
                'contentOptions' => function ($model, $key, $index, $column) {
                return ['style' => 'background-color:'.$model->color.'; color:'.$model->color_fuente];
                }
              ],
            //'estado',
            //'color_fuente',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
