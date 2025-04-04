<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\bootstrap\Html;
use kartik\date\DatePicker;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
AppAsset::register($this);

?>
 <style>
 .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
	border:0;
	padding:0;
	margin-left:-0.00001;
}
th, td {
  padding: 5px;
}

.fuente_table {
   
    font-size: 8px;
}
 </style>
 <div class="row">
    	  <table class ="table table-bordered table-condensed"  style="background-color: white; font-size: 11px; width: 100%">
    	      <tr>
    	         <td width= '15%'><b>Nombres</b></td><td width='35%'><?= $empleado->nombres?></td><td width='15%' ><b>Cedula</b></td><td width='35%'><?= $empleado->id_sys_rrhh_cedula?></td>
    	      </tr>
    	      <tr>
    	        <td width= '15%'><b>Desde</b></td><td width='35%'><?= $fechaini?></td><td width='15%' ><b>Hasta</b></td><td width='35%'><?= $fechafin?></td>
    	      </tr>
    	  </table>
 </div>
 <div class = "row">
  <div class = "col-md-12">
     <?= $this->render('_tableasistemp', ['datos' => $datos, 'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 10px; width: 100%"]);?>
  </div>
 </div>







