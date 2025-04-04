<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
$this->title = 'Periodo a pagar';
$this->render('../_alertFLOTADOR'); 
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2  col-md-offset-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?=$anio?>" name = 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                   'options' =>[ $mes => ['selected' => true]]
                  ]);
               ?>
       </div>
       <div class= 'col-md-2'>
            <?php
             echo '<label>Periodo</label>';
             echo  Html::dropDownList('periodo', 'periodo', $periodos,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 
                     'options' =>[ $periodo => ['selected' => true]]
                     
                 ]);
             ?>
       </div>
       <div class= 'col-md-2'>
            <?php
             echo '<label>Tipo de pago</label>';
             echo  Html::dropDownList('tipopago', 'tipopago', $tipospagos,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 
                     'options' =>[ $tipopago => ['selected' => true]]
                     
                 ]);
             ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
    <?php ActiveForm::end(); ?>
</div>
 <?php if(count($datos) > 0):?>
      <div class ="row" >
          <div class="col-md-12">
            <?=  Html::a('Exportar a PDF', ['infotipopagopdf','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'tipopago'=> $tipopago], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
           </div>
      </div>
      <br>
    <div class="row">
       <div class=  "col-md-12">
          
                <?= $this->render('_tablestipopago', ['datos'=> $datos, 'estilo'=> 'background-color: white; font-size: 11px; width: 100%;']);?>
          
       </div>
    </div>
 <?php endif;?>