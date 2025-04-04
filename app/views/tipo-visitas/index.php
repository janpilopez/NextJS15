<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysAccesoTipoVisitasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo de Visitas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-acceso-tipos-visitas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Tipo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_tipo_visita',
            'tipo_visita',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
