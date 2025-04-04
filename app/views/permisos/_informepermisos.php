<?php
use app\models\SysRrhhPermisos;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\SysAdmAreas;
use kartik\date\DatePicker;
use app\models\SysRrhhEmpleados;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
$this->title = 'Informe Permisos Empleados';
$this->render('../_alertFLOTADOR');
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
   <div class= 'col-md-2'>
              <?php
                echo '<label>Desde</label>';
                echo DatePicker::widget([
                	'name' => 'fechaini', 
                	'value' => $fechaini,
                    'options' => ['id'=>'fechainicio','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
            <div class= 'col-md-2'>
              <?php
                echo '<label>Hasta</label>';
                echo DatePicker::widget([
                	'name' => 'fechafin', 
                	'value' => $fechafin,
                    'options' => ['id'=>'fechafin','placeholder' => 'Seleccione..', 'class'=> 'form-control input-sm'],
                	'pluginOptions' => [
                		'format' => 'yyyy-mm-dd',
                		//'todayHighlight' => true
                	]
                ]);?>
           </div>
       <div class= 'col-md-3'>
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
   <div class= 'row'>
            <div class = 'col-md-3'>
              <?php echo '<label>Tipo de Permiso</label>';
                    echo   Html::DropDownList('tipo', 'tipo', 
                        ArrayHelper::map(SysRrhhPermisos::find()->all(), 'id_sys_rrhh_permiso', 'permiso'), ['class'=>'form-control input-sm', 'id'=>'tipo', 'prompt' => 'Todos', 'options'=>[ $tipo => ['selected' => true]]])
                ?>
                
            </div>
           </div>
   <div class= 'row'>
      <div class = 'col-md-12'>
          <?php 
            echo '<label>Nombres</label>';
            echo  Html::textInput('nombres', $filtro, ['class'=> 'form-control input-sm']);
          ?>
      </div>
    </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   <br>
   <?php ActiveForm::end(); ?>
   <?php if($datos): ?>
    <div class ="row" >
      <div class="col-md-12">
         <?=  Html::a('Exportar a Excel', ['informepermisosxls','fechaini'=> $fechaini, 'fechafin'=> $fechafin,'area'=> $area, 'departamento'=> $departamento,'filtro'=>$filtro,'tipo'=>$tipo], ['class'=>'btn btn-xs btn-success pull-right', "target" => "_blank" ]);?>
      </div>
    </div>
  	<br>
    <div class= 'row'>
      <div class= 'col-md-12'>
        <?php  echo $this->render('_tableinforme', ['datos'=> $datos, 'datos_medicos' => $datos_medicos,'fechaini'=> $fechaini, 'fechafin'=> $fechafin,'filtro' => $filtro,  'style' => "background-color: white; font-size: 12px; width: 100%"]);?>
      </div>
    </diV>
   <?php endif;?>
</div>