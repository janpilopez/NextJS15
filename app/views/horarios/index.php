<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhHorarioCabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Horarios Laborales';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-horario-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Horario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_rrhh_horario_cab',
           // 'horario',
            [
                'label'=>'Horario',
                'attribute'=>'horario',
                'contentOptions' => function ($model, $key, $index, $column) {
                return ['style' => 'background-color:'.$model->color];
                }
                ],
            'hora_inicio',
            'hora_fin',
            'hora_lunch',
            //'color',
            //'estado',
            //'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
