<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysAdmAreasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eventos/Capacitaciones';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-rrhh-eventos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Evento/Capacitación', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id_sys_adm_area',
            'nombreEvento',
            'responsableEvento',
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){
                
                return $model->estado == true ? 'Activo':'Inactivo';
                
                } ,
            ],
           // 'id_sys_empresa',
          //  'estado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
