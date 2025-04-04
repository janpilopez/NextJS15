<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhRubrosGastosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rubros Gastos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-rubros-gastos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Rubros', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id_sys_rrhh_rubros_gastos',
            'rubro',
             [
                'label'=>'Detalle',
                'attribute'=>'detalle',
                'value'=> function($model){
                
                  return $model->detalle;
                
                } ,
              ],
           // 'detalle',
            'max_gasto',
           // 'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
