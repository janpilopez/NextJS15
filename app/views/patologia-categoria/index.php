<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Patología - Categorías';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-patologia-categoria-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nueva Categoría', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'categoria',
            //'activo',
            [
                'label'=>'Estado',
                'attribute'=>'activo',
                'value'=> function($model){
                return $model->activo == true ? 'Activo' : 'Inactivo';
                } ,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
