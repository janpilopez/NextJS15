<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\SysAdmAreas;
$this->title = 'Info - Canastas Navideñas';
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= "row">
     <div class = "col-md-4 col-md-offset-3">
         <?php 
           echo '<label>Area</label>';
           echo   Html::DropDownList('area', 'area', 
           ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control', 'id'=>'area', 'prompt' => 'Todos', 'options'=>[ $area => ['selected' => true]]])
          ?>
     </div>  
      <div class = "col-md-2">
         <?php 
           echo '<label>Año</label>';
           echo   Html::input('number', 'anio', $anio , ['class'=>'form-control']);
          ?>
     </div>        
   </div>
   <br>
   <div class ="row">
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <?php ActiveForm::end(); ?>
</div>
  <?php if($datos): ?>
      <div class= 'row'>
        <div class = 'col-md-12'>
           <?=  Html::a('Exportar a pdf', ['informecanastapdf','area'=> $area, 'anio'=> $anio], ['class'=>'btn btn-xs btn-danger pull-right', 'target'=> '_blank' ]);?>
        </div>
     </div>
     <br>
     <div class= 'row'>
        <div class= 'col-md-12'>
            <?php echo $this->render('_tablecanastas', ['datos'=> $datos]);?>
        </div>
     </div>
     
  <?php endif;?> 

