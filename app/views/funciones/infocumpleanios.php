<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\SysAdmAreas;
$this->title = 'Lista Cumpleaños';
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= "row">
     <div class = "col-md-4 col-md-offset-4">
         <?php 
           echo '<label>Mes</label>';
           echo   Html::DropDownList('mes', 'mes', $meses, ['class'=>'form-control', 'id'=>'area', 'prompt' => 'Todos', 'options'=>[ $mes => ['selected' => true]]])
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
           <?=  Html::a('Exportar a pdf', ['cumpleaniospdf','mes'=> $mes], ['class'=>'btn btn-xs btn-danger pull-right', 'target'=> '_blank' ]);?>
           <?=  Html::a('Exportar a Excel', ['cumpleaniosxlsx','mes'=> $mes], ['class'=>'btn btn-xs btn-success pull-right', 'target'=> '_blank' ]);?>
        </div>
     </div>
     <br>
     <div class= 'row'>
        <div class= 'col-md-12'>
            <?php echo $this->render('_tablecumpleanios', ['datos'=> $datos]);?>
        </div>
     </div>
     
  <?php endif;?>