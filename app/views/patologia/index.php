<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysMedPatologiaCategoria;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysMedPatologiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Patologías';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-patologia-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nueva Patología', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
         
            [
                'label'=>'Categoría',
                'attribute'=>'id_sys_med_patologia_categoria',
                'filter'=>  ArrayHelper::map(SysMedPatologiaCategoria::find()->all(), 'id', 'categoria'),
                'format' => 'raw',
                'value'=> function($model){
                        $categoria  = SysMedPatologiaCategoria::find()->Where(['id'=> $model->id_sys_med_patologia_categoria])->one();
                        return $categoria->categoria;
                }
            ],
            'nombre',
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
