<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAdmHistorialSueldoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Historial Sueldos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-adm-historial-sueldo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nuevo Registro', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'anio',
            'sueldo_sectorial',
            'sueldo_basico',
            //'activo',
           //'user_created',
            //'date_created',
            //'user_autorization',
            //'date_autorization',
            //'autorizado',

            ['class' => 'yii\grid\ActionColumn',
                
                'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{autorizar}',
                'buttons'=>[
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Ver',
                        ]);
                    },
                    'update' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => 'Autorizar',
                        ]);
                    },
                    'autorizar' => function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                            'title' => 'Autorizar',
                            'data' => [
                                'confirm' => 'Está seguro que desea autorizar el siguiente registro?',
                            ],
                        ]);
                    },
                    ],
                    'urlCreator'=>function($action,$data){
                    if($action=='view'){
                        return ['historial-sueldo/view','anio'=>$data->anio];
                    }
                    if($action == 'update'){
                        return ['historial-sueldo/update','anio'=>$data->anio];
                    }
                    if($action == 'autorizar'){
                        return ['historial-sueldo/autorizar','anio'=>$data->anio];
                    }
                 }
                
            ],
        ],
    ]); ?>


</div>
