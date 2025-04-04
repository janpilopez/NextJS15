<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysEmpresaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Empresas';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="sys-empresa-index">
   
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nueva Empresa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id_sys_empresa',
            'razon_social',
            'ruc',
            'representante',
            'telefono',
            'pais',
            //'logo',
          
            //'fax',
            //'direccion',
            //'ciudad',
            //'celular',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
