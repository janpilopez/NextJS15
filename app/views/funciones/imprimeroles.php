<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use app\models\SysAdmAreas;
use app\models\SysRrhhEmpleados;
use kartik\depdrop\DepDrop;
$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\EnviarolesAsset;
EnviarolesAsset::register($this);
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
$this->title = 'Impresion de  Roles';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
?>


<style>
@media print{
	* { -webkit-print-color-adjust: exact; }
	
	.no-print, footer{
		display:none !important;
	}
	.container{
		width: 100%;
		padding-top:0 !important;
	}


    .without-margin{
        margin:0 !important;
        padding-left: 15px !important;
        padding-right: 15px !important;
        
    }
    h2, h3{
    	margin:0px !important;
    }
    td {
    	margin: 0px !important;
    	padding:1px !important;
    }
    .detalle-rol{
    	
    	border:1px solid black !important;
    	height: 190px !important;
    	
}
}
</style>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row no-print'>
   <h1  class ='no-print'><?= Html::encode($this->title)?></h1>

       <div class= 'col-md-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?= $anio?>" name = 'anio' id= 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 'id'=> 'mes',
                   'options' =>[ 'id'=>'mes',$mes => ['selected' => true]]
                  ]);
               ?>
       </div>
       <div class= 'col-md-2'>
            <?php
             echo '<label>Periodo</label>';
             echo  Html::dropDownList('periodo', 'periodo', $periodos,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 'id'=> 'periodo',
                     'options' =>[$periodo => ['selected' => true]]
                     
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
   <div class = "row no-print">
                     <div class = 'col-md-3'>
                       <label>Cédula</label>
                       <?php  
                       $template = '<div style="font-size:10px;"><div class="repo-language">{{nombres}}</div>' .
                           '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                       
                       echo Typeahead::widget([ 
                           'name' => 'cedula',
                           'options' => [ 'id'=> 'cedula', 'placeholder' => 'Buscar..',  'class'=> 'form-control input-sm'],
                           'pluginOptions' => ['highlight'=>true],
                           'scrollable'=>true,
                           'dataset' => [
                               [
                                   
                                   'remote' => [
                                       'url' =>    Url::to(['consultas/empleadosrol']) . '?q=%QUERY',
                                       'wildcard' => '%QUERY'
                                   ],
                                   'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                   'display' => 'value',
                                   'templates' => [
                                       'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                       'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                       ],
                                      
                               ]
                              
                               ],'pluginEvents' => [
                                   'typeahead:select' => 'function(ev, suggestion) {
                                       console.log(suggestion);
                                     $("#nombres").val(suggestion.nombres);
                                      }',
                               ]
                       ]); 
                    ?>
                   </div>
                    <div class = 'col-md-5'>
                       <?php echo html::label('Nombres')?>
                       <?php echo html::textInput('nombres', '', ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
                   </div>
   </div>
   <br>
   <div class ='row no-print'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
    <?php ActiveForm::end(); ?>
           
  <?php if($datos):
      
           
           foreach ($datos as $data):
           
    
                           $empleado =  (new \yii\db\Query())->select(
                                    [
                                        "empleados.id_sys_rrhh_cedula",
                                        "empleados.nombres",
                                        "fecha_ingreso",
                                        "cargo.cargo",
                                        "empleados.id_sys_adm_cargo",
                                        "(select cantidad  from  sys_rrhh_empleados_rol_mov where anio =  rol_mov.anio and mes = rol_mov.mes and id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula  and id_sys_rrhh_concepto = '".getConcepto($periodo)."') as cantidad",
                                        "( case empleados.id_sys_rrhh_forma_pago when 'T' then 'Tarjeta Virtual' when 'R' then 'Cta.Corriente'  when 'A' then 'Cta.Ahorros'   when 'C' then 'Cheque'  else 'Efectivo' end ) as forma_pago",
                                        "cta_banco",
                                        "banco",
                                        "empleados.email",
                                        "rol_mov.id_sys_empresa"
                                    ])
                                    ->from("sys_rrhh_empleados_rol_mov as  rol_mov")
                                    ->join("INNER JOIN", "sys_rrhh_empleados_rol_cab as rol_cab","rol_cab.anio=rol_mov.anio")->andwhere("rol_cab.mes=rol_mov.mes")->andwhere("rol_cab.periodo=rol_mov.periodo")->andwhere("rol_cab.id_sys_empresa=rol_mov.id_sys_empresa")
                                    ->join("INNER JOIN", "sys_rrhh_empleados as empleados","empleados.id_sys_rrhh_cedula=rol_mov.id_sys_rrhh_cedula")->andwhere("empleados.id_sys_empresa=rol_mov.id_sys_empresa")
                                    ->join("INNER JOIN", "sys_rrhh_empleados_contratos as contratos","contratos.id_sys_rrhh_cedula=empleados.id_sys_rrhh_cedula")->andwhere("contratos.id_sys_empresa=empleados.id_sys_empresa")
                                    ->join("INNER JOIN", "sys_adm_cargos as cargo","empleados.id_sys_adm_cargo = cargo.id_sys_adm_cargo")->andwhere("cargo.id_sys_empresa = empleados.id_sys_empresa")
                                    ->join("INNER JOIN", "sys_rrhh_bancos as banco","banco.id_sys_rrhh_banco=empleados.id_sys_rrhh_banco")->andwhere("banco.id_sys_empresa=empleados.id_sys_empresa")
                                    
                                    ->Where("rol_mov.anio = '{$anio}'")
                                    ->andwhere("rol_mov.mes= '{$mes}'")
                                    ->andwhere("rol_mov.periodo = '{$periodo}'")
                                    ->andwhere("rol_mov.id_sys_empresa= '001'")
                                    ->andwhere("empleados.id_sys_rrhh_cedula = '{$data['id_sys_rrhh_cedula']}'")
                                    ->orderBy("fecha_ingreso desc")
                                    ->one(SysRrhhEmpleados::getDb());
                                    
                              if($empleado):
                                 echo  $this->render('_rolpago',  ['datos'=> $empleado, 'anio'=> $anio, 'mes'=> $mes, 'periodo'=>$periodo]);
                                 echo "<br>";
                                 //echo "<br>";
                                 //echo  $this->render('_rolpago',  ['datos'=> $empleado, 'anio'=> $anio, 'mes'=> $mes, 'periodo'=>$periodo]);
                              endif;
          
              endforeach; 
                 
   endif; 
   
   
   
   
  function getConcepto ($periodo){
       
       $concepto = '';
       
       
       switch ($periodo) {
           
           case 1:
               
               $concepto = "ANTICIPO";
               break;
               
           case 2:
               
               $concepto = "SUELDO";
               break;
               
           case 90:
               
               $concepto = "VACACIONES";
               break;
               
           case 70:
               
               $concepto = "PAGO_DECIMO_TER";
               break;
               
           case 71:
               
               $concepto = "PAGO_DECIMO_CUA";
               break;
               
       }
       
       
       return $concepto;
       
   }
   
   
   
   
   ?>
   
   
   
  
