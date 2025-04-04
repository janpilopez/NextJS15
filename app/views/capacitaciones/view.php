<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmAreas */

$this->title = 'Eventos/Capacitaciones';
$this->params['breadcrumbs'][] = ['label' => 'Eventos/Capacitaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver';
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-eventos-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <div class = 'panel panel-default'>
   <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'idEvento',
            'nombreEvento',
            'responsableEvento',
            [
              'label'=>'Estado',
              'attribute'=>'estado',
              'value'=> function($model){
              
              return $model->estado == true ? 'Activo':'Inactivo';
              
              } ,
          ],
        ],
    ]) ?>
  </div>

</div>
