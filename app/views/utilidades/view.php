<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhUtilidadesCab */

$this->title = 'Utilidades Año : '.$model->anio;
$this->params['breadcrumbs'][] = ['label' => 'Utilidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-rrhh-utilidades-cab-view">
   
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
     <?php $form = ActiveForm::begin(); ?>
      <div class="col-md-3">
       <?php 
         echo '<label>Empresas</label>';
         echo  Html::dropDownList('empresa', 'empresa', ArrayHelper::map( $empresas, 'razon_social', 'razon_social'),
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                   'options' =>[ $empresa => ['selected' => true]]
                  ]);
        ?>
      </div>
      <div class="col-md-3">
       <?php 
         echo '<label>Estado</label>';
         echo  Html::dropDownList('estado', 'estado', $estados,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                   'options' =>[ $estado => ['selected' => true]]
                  ]);
        ?>
      </div>
      <div class="col-md-3">
          <?php echo '<br>'; ?>
    	  <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
    <br>
    <div class = "row">
       <div class = "col-md-12">
      <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'anio',   
            [
                'label'=>'Estado',
                'attribute'=>'estado',
                'value'=> function($model){ 
                    return  $model->estado == 'P' ? 'Liquidado': 'Generado';
                } ,
            ],  
            'fecha',
            'valor_uti',
            'valor_uti_empleado',
            [
                'label'=>'($) Valor Empleado',
                'attribute'=>'valor_uti_empleado',
                'value'=> function($model){
                
                return number_format(($model->valor_uti_empleado/($model->valor_uti_empleado +$model->valor_uti_carga))*$model->valor_uti, 2, '.', ',');
                
                } ,
            ],
            'valor_uti_carga',
            [
                'label'=>'($) Valor Carga',
                'attribute'=>'valor_uti_carga',
                'value'=> function($model){
                
                return number_format(($model->valor_uti_carga/($model->valor_uti_empleado +$model->valor_uti_carga))*$model->valor_uti, 2, '.', ',');
                
                } ,
           ],
       
        ],
          ]) ?>
       </div>
    </div>
    <div class ="row" >
      <div class="col-md-12">
        <?php if($model): ?>
     	   <?=  Html::a('Exportar a Excel', ['viewxls', 'id' =>  $model->anio, 'empresa'=> $empresa, 'estado' => $estado], ['class'=>'btn btn-xs btn-success pull-right' ]);?>
        <?php endif;?>
       </div>
   </div>
  <br>
    <div class ="row">
       <div class = "col-md-12">
          <?php if($modeldet): ?>
        	 <?php echo $this->render('_tableempleados', ['modeldet'=> $modeldet]);?>
          <?php endif;?>
       </div>
    </div>
</div>
