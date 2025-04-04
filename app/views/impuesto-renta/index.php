<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysRrhhImpuestoRentaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Impuesto Rentas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-impuesto-renta-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Impuesto Renta', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_impuesto_renta',
            'fraccion_basica',
            'fraccion_excedente',
            'impuesto_fraccion_basica',
            'impuesto_fraccion_excedente',
            //'id_sys_empresa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
