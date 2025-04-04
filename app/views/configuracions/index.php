<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysConfiguracionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configuracions';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-configuracion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Agregar Configuración', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_conf_cod',
            'parametro',
            'descripcion',
            [
                'label'=>'Detalle',
                'attribute'=>'detalle',
                'value'=> function($model){
                
                  return  utf8_encode($model->detalle);
                
                } ,
            ],
                
            //'detalle',
           // 'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
