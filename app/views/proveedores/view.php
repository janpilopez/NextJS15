<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Proveedores';
$this->params['breadcrumbs'][] = ['label' => 'Proveedores', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-acceso-proveedores-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                
                'attribute'=>'idProveedor',
                'value'=> function($model){
              
                  return str_pad($model->idProveedor, 5, "0", STR_PAD_LEFT);
                }
            ],
            'cedula',
            'nombreProveedor',
            [
              'attribute'=>'nivel_riesgo',
              'value'=> function($model){
              
                  if($model->nivel_riesgo == 1){
                      return 'Bajo';
                  }else if($model->nivel_riesgo == 2){
                      return 'Medio';
                  }else{
                      return 'Alto';
                  }    
              } ,
            ],

        ],
    ]) ?>
</div>
