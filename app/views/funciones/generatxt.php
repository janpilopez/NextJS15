<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
$this->title = 'Generar Archivo Banco';
$this->render('../_alertFLOTADOR'); 
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?=  $anio?>" name = 'anio'>
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
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos', 'options'=>[ $area => ['selected' => true]]])
              ?>
       </div> 
       <div class = 'col-md-3'>
               <?php echo '<label>Departamento</label>';
                     echo DepDrop::widget([
                       'name'=> 'departamento',
                         'data'=> [$departamento => 'departamento'],
                       'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                       'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                       'pluginOptions'=>[
                           'depends'=>['area'],
                           'initialize' => true,
                           'initDepends' => ['area'],
                           'placeholder'=>'Todos',
                           'url'=>Url::to(['/consultas/listadepartamento']),
                           
                       ]
                   ]);
               ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Generar Archivo</button>
         </div>
   </div>
    <?php ActiveForm::end(); ?>
</div>
