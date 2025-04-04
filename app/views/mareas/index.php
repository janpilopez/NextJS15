<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysRrhhMareasCab;
use app\models\SysRrhhBarcos;
use app\models\SysAdmDepartamentos;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SysRrhhMareasCabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mareas';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-rrhh-mareas-cab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Marea', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sys_rrhh_mareas_cab',
            [
                'label'=>'Barco',
                'attribute'=>'id_sys_rrhh_barcos',
                'filter'=> ArrayHelper::map(SysAdmDepartamentos::find()->orderBy('departamento')->all(), 'id_sys_adm_departamento', 'departamento') ,
                'format' => 'raw',
                'value'=> function($model){
                
                    $departamento  = SysAdmDepartamentos::find()->Where(['id_sys_adm_departamento'=> $model->id_sys_rrhh_barcos])->one();
                    return $departamento->departamento ;
                    
                }
                ],
            
            
            'fecha_inicio',
            'fecha_fin',
            'tonelada',
            'valor_tonelada',
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'contentOptions'=>['style'=>'width: 5%;'],
                'filter'=> ['A' => 'Abierta','C' => 'Cerrada'],
                'format' => 'raw',
                'value'=> function($model){
                
                     return $model->estado == 'A' ? 'Abierta': 'Cerrada'; 
                
                }
                
            ],
            //'id_sys_rrhh_barcos',
            //'usuario_creacion',
            //'fecha_creacion',
            //'usuario_actualizacion',
            //'fecha_actualizacion',
            
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
