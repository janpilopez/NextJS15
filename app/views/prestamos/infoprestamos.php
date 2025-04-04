<?php
/* @var $this yii\web\View */
use kartik\number\NumberControl;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use kartik\date\DatePicker;
use app\assets\PrestamoAsset;
PrestamoAsset::register($this);
$this->title = 'Listado de Préstamos';

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
  <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
        <div class= 'col-md-2 col-md-offset-2 text-center '>
        <?php
                echo '<label>Año Inicio</label>';
                echo NumberControl::widget([
                  'name' => 'fechaini',
                  'value' => $fechaini,
                  'options' => ['id'=>'fechaini'],
                	'maskedInputOptions' => [
                    'groupSeparator' => '',
                    'digits' => 0,
                    'min' => 2000,
                    'max' => 2060,
                    'rightAlign' => false
                  ],
                  'displayOptions' =>  [
                    'placeholder' => '',
                    'disabled'=> true,
                  ],
                ]);?>
       </div>
       <div class= 'col-md-2 text-center'>
                <?php
                echo '<label>Año Fin</label>';
                echo NumberControl::widget([
                  'name' => 'fechafin',
                  'value' => $fechafin,
                  'options' => ['id'=>'fechafin'],
                	'maskedInputOptions' => [
                    'groupSeparator' => '',
                    'digits' => 0,
                    'min' => 2000,
                    'max' => 2060,
                    'rightAlign' => false
                  ],
                  'displayOptions' =>  [
                    'placeholder' => '',
                    'disabled'=> true,
                  ],
                ]);?>
       </div>
        <div class='col-md-3 text-center'>
            <?php echo '<label>Tipo</label>';?>
            <?php echo  html::dropDownList('tipo', null, ['A'=> 'Año Actual', 'U'=> 'Período de Tiempo'], ['class'=> 'form-control', 'id' => 'tipo' ,'options' =>[ $tipo => ['selected' => true]]]);?>
        </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <?php ActiveForm::end(); ?>

  <?php if($datos): ?>
  <div class ="row" >
      <div class="col-md-12">
        <?=  Html::a('Exportar a Excel', ['prestamosxls','fechaini'=> $fechaini, 'fechafin' => $fechafin,'tipo' => $tipo], ['class'=>'btn btn-xs btn-success pull-right' ]);?>
       </div>
  </div>
  <br>
  <div class= 'row' >

  <?php 
    
    if($tipo == 'A'):
    
      echo $this->render('_tableprestamos', ['datos'=> $datos]);
      
    elseif($tipo == 'U'):
      
      echo $this->render('_tableprestamos2', ['datos'=> $datos]);

    endif;
    ?>
      
  </div>
  <?php endif;?> 

</div>