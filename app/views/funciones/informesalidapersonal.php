<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
use app\models\SysRrhhCausaSalida;
$this->title = 'Informe de Salida Personal';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2 col-md-offset-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?= $anio?>" name = 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes Inicio</label>';
             echo  Html::dropDownList('mesini', 'mesfin', $meses,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                   'options' =>[ $mesini => ['selected' => true]]
                  ]);
               ?>
       </div>
        <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes Fin</label>';
             echo  Html::dropDownList('mesfin', 'mesfin', $meses,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..',
                   'options' =>[ $mesfin => ['selected' => true]]
                  ]);
               ?>
       </div>
       <div class= 'col-md-3'>
         <?php echo '<label>Motivo</label>';
               echo   Html::DropDownList('id_sys_rrhh_causa_salida', 'id_sys_rrhh_causa_salida', 
               ArrayHelper::map(SysRrhhCausaSalida::find()->all(), 'id_sys_rrhh_causa_salida', 'descripcion'), ['class'=>'form-control input-sm', 'id'=>'id_sys_rrhh_causa_salida', 'prompt' => 'Todos',  'options'=>[ $id_sys_rrhh_causa_salida => ['selected' => true]]])
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
        <?=  Html::a('Exportar a PDF', ['salidapersonalpdf','mesini'=> $mesini, 'mesfin'=> $mesfin, 'anio'=> $anio, 'causa_salida'=> $id_sys_rrhh_causa_salida], ['class'=>'btn btn-xs btn-danger pull-right', "target" => "_blank" ]);?>
        <?=  Html::a('Exportar a EXCEL', ['salidapersonalxls','mesini'=> $mesini, 'mesfin'=> $mesfin, 'anio'=> $anio, 'causa_salida'=> $id_sys_rrhh_causa_salida], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
       </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tablesalidapersonal', [ 'mesini'=> $mesini, 'datos'=> $datos, 'anio'=> $anio]);?>
  </div>
  <?php endif;?> 

