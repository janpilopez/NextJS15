<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysMedCie10Search */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Codificación Cie10';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-cie10-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Cie10', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codigo',
            'descripcion',
            [
                'label'=>'Activo',
                'attribute'=>'activo',
                'value'=> function($model){
                
                 return  $model->activo == 1 ? 'SI': 'NO';
                 
                }
             ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
