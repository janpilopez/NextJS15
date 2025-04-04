<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
$this->title = 'Rol Detalle';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2'>
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
                  
                   /*echo  Html::DropDownList('departamento', 'departamento',
                         ArrayHelper::map(SysAdmDepartamentos::find()->andFilterWhere(['id_sys_adm_area'=> $area])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'id'=>'departamento', 'prompt' => 'Todos',  'options'=>[ $departamento => ['selected' => true]]])
                  */
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
        <?=  Html::a('Exportar a PDF', ['roldetallepdf','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento'=> $departamento], ['class'=>'btn btn-xs btn-danger pull-right',"target" => "_blank"  ]);?>
        <?=  Html::a('Exportar a Excel', ['roldetallexlsx','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento'=> $departamento], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px' ]);?>
        <?=  Html::a('Generar Consilidado', ['consolidadoxlsx','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento'=> $departamento], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px' ]);?>
         <?=  Html::a('Descargar H.Extras', ['horasextrasxlsx','anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento'=> $departamento], ['class'=>'btn btn-xs btn-success pull-right', 'style'=> 'margin-right: 5px', ]);?>
      </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableroldetalle', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);?>
  </div>
  <?php endif;?> 

