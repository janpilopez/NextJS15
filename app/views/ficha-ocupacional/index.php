<?php

use app\models\SysRrhhEmpleados;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysMedFichaOpupacionalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->render('../_alertFLOTADOR');
$this->title = 'Ficha Ocupacional';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-med-ficha-opupacional-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Ficha', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_sys_rrhh_cedula',
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                    $empleado        = SysRrhhEmpleados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    return $empleado->nombres;
                } ,
            ],
            'secuencial',
            'tipo',
            [
                'label'=>'Tipo',
                'attribute'=>'tipo',
                'filter'=>  ['P'=> 'PRE-OCUPACIONAL', 'I'=>'INICIO', 'P'=> 'PERIODICA','R'=> 'RETIRO', 'G'=> 'REINTEGRO'],
                'value'=> function($model){
                
                    $tipo = 'REINTEGRO';
                    
                    if($model->tipo == 'P'):
                    
                        $tipo = 'PRE-OCUPACIONAL';
                    
                    elseif ($model->tipo == 'I'):
                    
                        $tipo = 'INICIO';
                    
                    elseif ($model->tipo == 'P'):
                    
                        $tipo = 'PERIODICA';
               
                    elseif ($model->tipo == 'R'):
                    
                        $tipo = 'RETIRO';
                    
                    endif;
                
                return $tipo;
                
                } ,
            ],
            'fecha',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
