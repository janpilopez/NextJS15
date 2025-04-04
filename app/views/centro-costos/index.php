<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAdmCcostosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Centro de costos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR');
?>
<div class="sys-adm-ccostos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Centro de Costos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_adm_ccosto',
            'centro_costo',
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){
                
                return $model->estado == 'A'? 'Activo':'Inactivo';
                
                } ,
             ],
           // 'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
