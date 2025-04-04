<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;
use kartik\date\DatePicker;
$url = Yii::$app->urlManager->createUrl(['comedor']);
$inlineScript = "var url='$url'";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\InfoComedorAsset;
InfoComedorAsset::register($this);
$this->title = 'Informe Lunch';
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= "row">
     <div class = "col-md-3 col-md-offset-2">
                    <label>Fecha Inicio</label>
                   <?php echo DatePicker::widget([
                	'name' => 'fechaini', 
                    'value' => $fechaini,
                	'options' => [ 'id'=> 'fechaini','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
     
     
     </div>   
     <div class = "col-md-3">
                    <label>Fecha Fin</label>
                   <?php echo DatePicker::widget([
                	'name' => 'fechafin', 
                    'value' => $fechafin,
                	'options' => [ 'id'=> 'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	   ]
                    ]);?>
     
     
     </div> 
     <div class = "col-md-3">
                <label>Tipo</label>
                <?=  html::dropDownList('tipo', $tipo, $lunch, ['class'=> 'form-control input-sm']) ?>
     </div>   
   </div>
   <br>
   <div class ="row">
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <?php ActiveForm::end(); ?>
   <div id="loading"></div>
</div>
<?php if($datos): ?>    
     <div class= "row">
        <div class = "col-md-12">
          <!--<?=  Html::a('Exportar a pdf', ['infolunchpdf','fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo'=> $tipo, 'tipoinfo'=> $tipoinfo], ['class'=>'btn btn-xs btn-danger pull-right', 'target'=> '_blank' ]);?>-->
        </div>
     </div>
     <br>
     <div class= "row">
        <div class= "col-md-12">
          <table class="table table-bordered table-condensed" style="background-color: white; font-size: 11px; width: 100%;"> 
          <thead>
            <?php 
            if($tipo == 1): ?>
              <tr>
                 <th>No</th>
                 <th>Area</th>
                 <th>Departamento</th>
                 <th>Nombres</th>
                 <th>Identificación</th> 
                 <th>Hora Ingreso</th>
                 <th>Hora Lunch</th> 
                 <th>Acción</th>   
              </tr>
            <?php endif; ?>
            <?php 
            if($tipo == 3): ?>
              <tr>
                 <th>No</th>
                 <th>Area</th>
                 <th>Departamento</th>
                 <th>Nombres</th>
                 <th>Identificación</th> 
                 <th>Hora Salida</th>
                 <th>Hora Lunch</th> 
                 <th>Acción</th>   
              </tr>
            <?php endif; ?>
           </thead>
           <tbody>
           <?php 
            if($tipo == 1):
               foreach ($datos as $index =>$data): ?>
                  <tr bgcolor= "<?= date('H:i', strtotime($data['hora'])) >  '08:00:00' ? '#FAAC9B': ''?>">
                     <td><?=$index +  1?></td>
                     <td><?=$data['area']?></td>
                     <td><?=$data['departamento']?></td>
                     <td><?=$data['nombres']?></td>
                     <td><?=$data['id_sys_rrhh_cedula']?></td> 
                     <td><?=date('H:i', strtotime($data['ingreso']))?></td> 
                     <td><?=date('H:i', strtotime($data['hora']))?></td>   
                     <td>
                        <?php if(date('H:i', strtotime($data['hora'])) >  '08:00:00'):
                           echo Html::checkbox('chkAddDescuento', false, ['onclick'=> "addDescuento('{$data['id_sys_rrhh_cedula']}', '{$data['fecha']}')"]);
                        endif; ?> 
                     </td>   
                  </tr>
               <?php endforeach;
            endif;?>
            <?php 
            if($tipo == 3):
               foreach ($datos as $index =>$data): ?>
                  <tr bgcolor= "<?= date('Y-m-d H:i', strtotime($data['salida'])) <  date('Y-m-d', strtotime($data['fecha'])).' 19:00' ? '#FAAC9B': ''?>">
                     <td><?=$index +  1?></td>
                     <td><?=$data['area']?></td>
                     <td><?=$data['departamento']?></td>
                     <td><?=$data['nombres']?></td>
                     <td><?=$data['id_sys_rrhh_cedula']?></td> 
                     <td><?=date('H:i', strtotime($data['salida']))?></td> 
                     <td><?=date('H:i', strtotime($data['hora']))?></td>   
                     <td>
                        <?php if(date('Y-m-d H:i', strtotime($data['salida'])) <  date('Y-m-d', strtotime($data['fecha'])).' 19:00'):
                           echo Html::checkbox('chkAddDescuento', false, ['onclick'=> "addDescuento('{$data['id_sys_rrhh_cedula']}', '{$data['fecha']}')"]);
                        endif; ?> 
                     </td>   
                  </tr>
               <?php endforeach;
            endif;?>
           </tbody>
           <tbody>
          </table>
        </div>
     </div>
  <?php endif;?> 

