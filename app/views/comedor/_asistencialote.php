<?php
/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\web\View;
use app\models\SysRrhhComedor;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
$this->title = 'Consumo Comedor por Lote';
use app\assets\ComedorLoteAsset;
ComedorLoteAsset::register($this);
$url = Yii::$app->urlManager->createUrl(['comedor']);
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
                <div class="col-md-3 col-md-offset-2 text-center" >
                  <label>Ingreso</label>
                   <?php echo DateTimePicker::widget([
                	'name' => 'fechaingreso', 
                    'value' => date('Y-m-d H:i'),
                	'options' => [ 'id'=> 'fechaingreso','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd hh:ii',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
                </div>
                <div class="form-group col-md-3 text-center">
                  <label>Salida</label>
                   <?php echo DateTimePicker::widget([
                	'name' => 'fechasalida', 
                    'value' => date('Y-m-d H:i'),
                	'options' => [ 'id'=> 'fechasalida','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd hh:ii',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
                </div>
                <div class="form-group col-md-3 text-center">
                  <label>Alimento</label>
                  <select class="form-control input-sm" id=  "tipoalimento">
                    <option selected>Seleccione</option>
                    <option value = '1'>Desayuno</option>
                    <option value = '2'>Almuerzo</option> 
                    <option value = '3'>Merienda</option> 
                  </select>
                </div>
           </div>
              </div>
              <div class = "form-group col-md-12 text-center">
             	 <button type="submit" class="btn btn-primary" id="btn-guardar">Guardar Alimentos</button>
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

