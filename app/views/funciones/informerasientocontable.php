<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use yii\web\View;
use kartik\depdrop\DepDrop;
use app\assets\AsientoContableAsset;
AsientoContableAsset::register($this);

$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Elaboración Asiento Contable';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$areas = [5 => 'MATERIA PRIMA', 4=> 'UNIDAD DE CALIDAD HIGIENE Y PRODUCTO FINAL', 2=> 'PRODUCCION', 6=>'GESTION INTEGRAL', 3=>'MANTENIMIENTO', 1=>'ADMINISTRACION'];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?=  date('Y')?>" name = 'anio'>
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
             <?php echo '<label>Área</label>';
                   echo Html::dropDownList('area', 'area', $areas,
                   ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                     'options' =>[ $area => ['selected' => true]]
                    ]);
              ?>
           </div> 
           <div class= 'col-md-2'>
             <?php echo '<label>Periodo</label>';
                   echo   Html::DropDownList('periodo', null, 
                   [2=> 'Salarios', 90=> 'Provisiones'], ['class'=>'form-control input-sm', 'id'=>'area', 'options'=>[ $periodo => ['selected' => true]]])
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
  <?php if($datos): ?>
  <div class ="row" >
      <div class="col-md-12">
        <?=  Html::a('Exportar a Excel', ['informeasientocontablexls','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px' ]);?>
      </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableasientocontable', ['meses'=> $meses, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'datos'=> $datos, 'anio'=> $anio]);?>
  </div>
  <?php endif;?> 

