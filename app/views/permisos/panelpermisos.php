<?php

use yii\helpers\Html;
use yii\web\View;
$url = Yii::$app->urlManager->createUrl(['permisos']);

$datos =  json_encode($datos);

$inlineScript = "var url='$url', datos = {$datos};";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\PermisosAsset;
PermisosAsset::register($this);
$this->title = 'Permisos Empleados';
$this->params['breadcrumbs'][] = $this->title;
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 =>'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhComedor */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
.clockdate-wrapper {
    background-color: #333;
    padding:25px;
    max-width:300px;
    width:100%;
    text-align:center;
    border-radius:5px;
    margin:0 auto;
    margin-top:15%;
}
#clock{
    background-color:#333;
    font-family: sans-serif;
    font-size:40px;
    text-shadow:0px 0px 1px #fff;
    color:#fff;
}
#text{
    background-color:#fff;
    font-family: sans-serif;
    font-size:15px;
    text-shadow:0px 0px 1px #fff;
    color: #000;;
}
#clock span {
    color:#888;
    text-shadow:0px 0px 1px #333;
    font-size:30px;
    position:relative;
    top:-27px;
    left:-10px;
}
#date {
    letter-spacing:10px;
    font-size:14px;
    font-family:arial,sans-serif;
    color:#fff;
}
.titulo {

 font-size:250px;
 font-weight: bold;
}

.table-wrapper {
  width: 100%;
  height: 300px; /* Altura de ejemplo */
  overflow: auto;
}

.table-wrapper table {
  border-collapse: separate;
  border-spacing: 0;
}

.table-wrapper table thead {
  position: -webkit-sticky; /* Safari... */
  position: sticky;
  top: 0;
  left: 0;
}

.table-wrapper table thead th,
.table-wrapper table tbody td {
  background-color: #FFF;
}

</style>


<h1>Permisos Empleados del <?php echo date('d')." de ".$meses[date('n')] ?></h1>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
      <div class="col-md-12">
         <br>
         <div class="panel panel-default">
            <div class="panel-heading"></div>
               <div class="panel-body">
                  <div class="table-wrapper">
                     <table id="table" class="table" style="background-color: white; font-size: 16px; width: 100%;">
                        <thead>
                           <tr> 
                              <th>No. de Permiso</th>
                              <th>Departamento</th>
                              <th>C.I</th>
                              <th>Nombres</th>
                              <th>Hora Inicio</th>
                              <th>Hora Fin</th>
                           </tr>
                        </thead>
                           <tbody></tbody>
                     </table>
                  </div> 
               </div>
         </div>
      </div>
   </div>  
</div>

<h1>Permisos Equipos del <?php echo date('d')." de ".$meses[date('n')] ?></h1>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
            <div class="col-md-12">
               <br>
               <div class="panel panel-default">
                  <div class="panel-heading"></div>
                     <div class="panel-body">
                        <div class="table-wrapper">
                           <table id="table1" class="table" style="background-color: white; font-size: 16px; width: 100%;">
                              <thead>
                                 <tr> 
                                    <th>No. de Permiso</th>
                                    <th>Departamento</th>
                                    <th>C.I</th>
                                    <th>Nombres</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                           </table>
                        </div> 
                     </div>
            </div>
         </div>
   </div>  
</div>
      

<h1>Permisos Alimentos del <?php echo date('d')." de ".$meses[date('n')] ?></h1>
<div class="sys-rrhh-comedor-form">
   <div class = "row">
            <div class="col-md-12">
               <br>
               <div class="panel panel-default">
                  <div class="panel-heading"></div>
                     <div class="panel-body">
                        <div class="table-wrapper">
                           <table id="table2" class="table" style="background-color: white; font-size: 16px; width: 100%;">
                              <thead>
                                 <tr> 
                                    <th>No. de Permiso</th>
                                    <th>Departamento</th>
                                    <th>C.I</th>
                                    <th>Nombres</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                           </table>
                        </div> 
                     </div>
               </div>
            </div>
   </div>  
</div>
      
      