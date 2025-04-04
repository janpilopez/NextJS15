<?php
/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\web\View;
use kartik\date\DatePicker;
$this->title = 'Registro de Novedades';
use app\assets\FuncionesAsset;
FuncionesAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
  <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
   <div class= 'row'>
       <div class= 'col-md-12'>
            <form>
              <div class="form-row">
                <div class="form-group col-md-2">
                  <label >Año</label>
                  <input type="number" class="form-control input-sm" value = "<?=  date('Y')?>" id="anionovedad">
                </div>
                <div class="form-group col-md-2">
                  <label>Mes</label>
                  <select class="form-control input-sm" id ="mesnovedad">
                    <option selected>Seleccione</option>
                    <option value = '1'>Enero</option>
                    <option value = '2'>Febrero</option> 
                    <option value = '3'>Marzo</option> 
                    <option value = '4'>Abril</option> 
                    <option value = '5'>Mayo</option> 
                    <option value = '6'>Junio</option> 
                    <option value = '7'>Julio</option> 
                    <option value = '8'>Agosto</option> 
                    <option value = '9'>Septiembre</option> 
                    <option value = '10'>Octubre</option> 
                    <option value = '11'>Noviembre</option> 
                    <option value = '12'>Diciembre</option>       
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label>Periodo</label>
                  <select class="form-control input-sm" id ='periodonovedad'>
                    <option selected>Seleccione</option>
                    <option value = '2'>Mensual</option>
                    <option value = '1'>Quincenal</option> 
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label>Fecha de Novedad</label>
                   <?php echo DatePicker::widget([
                	'name' => 'fechanovedad', 
                    'value' => date('Y-m-d'),
                	'options' => [ 'id'=> 'fechanovedad','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
                </div>
                <div class="form-group col-md-3">
                  <label>Tipo de Novedad</label>
                  <select class="form-control input-sm" id=  "tiponovedad">
                    <option selected>Seleccione</option>
                    <option value = '1'>Prestámo Quirografario</option>
                    <option value = '2'>Prestamo Hipótecario</option> 
                    <option value = '3'>Descuento Farmarcia</option> 
                    <option value = '4'>Dscto. Venta Filetes</option> 
                    <option value = '5'>Descuento Todo Papeleria</option> 
                  </select>
                </div>
              </div>
              <div class = "form-group col-md-12 text-center">
             	 <button type="submit" class="btn btn-primary" id="btn-guardar">Guardar Novedades</button>
              </div>
            </form>
       </div>
   </div>
   <div class= 'row'>
        <div class= 'col-md-12'>
           <button class='btn btn-success input-sm'  id= 'btn-pegar'><i class="glyphicon glyphicon-plus"></i></button>
            <br>
           <table class = 'table' id='tabla' style='font-size: 11px;'>
             <thead>
             </thead>
             <tbody>
             
             </tbody>
           </table>
       </div>
  </div>
   <div id="loading"></div>
</div>

