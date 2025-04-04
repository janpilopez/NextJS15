<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\bootstrap\Html;
use kartik\date\DatePicker;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhEmpleadosPermisos;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
AppAsset::register($this);

$holgura =  15;

$cont = 0;
$totahorastrab = '00:00:00';
$totalh25      = '00:00:00';
$totalh50      = '00:00:00';
$totalh100     = '00:00:00';
$totalatraso   = '00:00:00';
$totalsaltemp  = '00:00:00';
$totalemp      = 0; 


$objarea =  SysAdmAreas::find()->where(['id_sys_adm_area'=> $area])->one();
$objdepar = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $departamento])->one();

//listado de funciones de calculos
echo $this->render('funciones');
$dias = [1 => 'Lunes',  2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
$tipoinforme = '';

if(trim($tipo) == 'F'):
   $tipoinforme = 'Faltas'; 
elseif(trim($tipo) == 'V'):
   $tipoinforme ='Vacaciones'; 
elseif(trim($tipo) == 'L'):
    $tipoinforme = 'Dia Libre';
elseif(trim($tipo) == 'P'):
    $tipoinforme = 'Permisos';
elseif(trim($tipo) == 'M'):
   $tipoinforme = 'Marcación';
endif;


//realizar los filtros 

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
    }
}


class FilterData {
    private $colName;
    private $value;
    
    function __construct($colName, $value) {
        $this->colName = $colName;
        $this->value = $value;
    }
    
    function getFilter($i) {
        return $i[$this->colName] == $this->value;
    }
}

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
        <div class="col-xs-12 text-center">
              <img src="logo/1391744064001/logo_reporte.jpg"  height="30" width="120">
         </div>
		<div class="col-xs-12">
			<h6 class="subtitle text-center"><b>Informe por <?= $tipoinforme ?></b></h6>
		</div>
 </div>
 <div class = "row" style="border: 1px solid black;">

    	  <table style="font-size: 11px; width: 100%">
    	      <tr>
    	         <td width= '15%'><b>Área</b></td><td width='35%'><?= $objarea== null ? 'Todos': $objarea->area?></td><td width='15%' ><b>Departamento</b></td><td width='35%'><?=  $objdepar== null ? 'Todos': $objdepar->departamento ?></td>
    	      </tr>
    	      <tr>
    	        <td width= '15%'><b>Desde</b></td><td width='35%'><?= $fechaini?></td><td width='15%' ><b>Hasta</b></td><td width='35%'><?= $fechafin?></td>
    	      </tr>
    	  </table>
 </div>
  <br>
 <div class= 'row'>
    
<table  class="table table-bordered table-condensed fuente_table" style="background-color: white; font-size: 10px; width: 100%">
    <thead>
      <tr>
        <th>No</th>
        <th>Nombres</th>
        <th>Entrada</th>
        <th>Salida</th>
        <th>T.Horas</th>
        <th>Atraso</th>
        <th>Sal.Temp</th>
        <th>H.25</th>
        <th>H.50</th>
        <th>H.100</th>
        <th>Observación</th>
      </tr>
    </thead>
    <tbody>
     <?php 
   
     while(strtotime($fechaini) <= strtotime($fechafin))
     {
         
      $dia = date("N", strtotime($fechaini));
         
     ?>
  
     <?php
     
                 $departamentos =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $datos));
                 sort($datos);
                 
                 foreach ($departamentos as $index => $Id): ?>
                 
                        <tr>
                            <th colspan = "11"><?= $fechaini .'-'.$dias[date("N", strtotime($fechaini))]?></th>
                        </tr>
                        <tr>
                          <th colspan = "11"><?= $Id?></th>
                        </tr>
                        <?php
                        
                        
                             $empleados = array_filter($datos, array(new FilterData("departamento", $Id), 'getFilter'));
                             $empleados = array_values($empleados);
                             $empleados =  orderMultiDimensionalArray ($empleados, "nombres");
                       
                             foreach ($empleados as $data):
                 
                              $entrada        = '00:00:00';
                              $salida         = '00:00:00';
                              $thoras         = '00:00:00';
                              $tipojorna      = 'N';
                              
                              $jornadaentra   = '00:00:00';
                              $atraso         = '00:00:00';
                              $saltemp        = '00:00:00';
                            
                              $h25           = '00:00:00';
                              $h50           = '00:00:00';
                              $h100          = '00:00:00';
                              
                              $observacion   = '';
                              $fecha_ent     = '';
                              $fecha_sal     = '';
                              
                              //validamos que busque la asistencia a partir de su fecha ingreso
                              $fechaingresoemp = getFechaIngreso($data['id_sys_rrhh_cedula']);
                              
                              
                              if($fechaingresoemp):
                              
                              
                              if(  $fechaini >= $fechaingresoemp['fecha_ingreso']):
                              
                              
                                           $modelmarcacion = SysRrhhEmpleadosMarcacionesReloj::find()->where(['fecha_jornada'=> $fechaini, 'id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])->andWhere(['estado'=> 'A'])->all();
                        
                                           if(!$modelmarcacion):
                                              
                                                     $observacion =   getJustificacion($fechaini, $data['id_sys_rrhh_cedula']);
                                                   
                                                     if($tipo == 'F' && $observacion == 'FALTA'):
                                                       $cont++;
                                                     ?> 
                                                        <tr>
                                                               <td><?= $cont ?></td>
                                                               <td><?= $data['nombres'] ?></td>
                                                               <td><?= $entrada?></td>
                                                               <td><?= $salida?></td>
                                                               <td><?= $thoras?></td>
                                                               <td><?= $atraso?></td>
                                                               <td><?= $saltemp?></td>
                                                               <td><?= $h25 ?></td>
                                                               <td><?= $h50 ?></td>
                                                               <td><?= $h100 ?></td>
                                                               <td><?= $observacion?></td>
                                                          </tr>
                                                 <?php elseif($tipo == 'V' && $observacion == 'GOZO DE VACACIONES'):
                                                      $cont++;
                                                 ?> 
                                                      
                                                         
                                                        <tr>
                                                               <td><?= $cont ?></td>
                                                               <td><?= $data['nombres'] ?></td>
                                                               <td><?= $entrada?></td>
                                                               <td><?= $salida?></td>
                                                               <td><?= $thoras?></td>
                                                               <td><?= $atraso?></td>
                                                               <td><?= $saltemp?></td>
                                                               <td><?= $h25 ?></td>
                                                               <td><?= $h50 ?></td>
                                                               <td><?= $h100 ?></td>
                                                               <td><?= $observacion?></td>
                                                          </tr>
                                          
                                   
                                                        <?php elseif($tipo == 'L' && $observacion == 'DIA LIBRE'):
                                                            $cont++;
                                                        ?>
                                                             
                                                                <tr>
                                                                       <td><?= $cont ?></td>
                                                                       <td><?= $data['nombres'] ?></td>
                                                                       <td><?= $entrada?></td>
                                                                       <td><?= $salida?></td>
                                                                       <td><?= $thoras?></td>
                                                                       <td><?= $atraso?></td>
                                                                       <td><?= $saltemp?></td>
                                                                       <td><?= $h25 ?></td>
                                                                       <td><?= $h50 ?></td>
                                                                       <td><?= $h100 ?></td>
                                                                       <td><?= $observacion?></td>
                                                                  </tr>
                                                    
                                                         <?php elseif($tipo == 'P'): 
                                                            
                                                                     $permiso = [];
                                                                     $permiso  = (new \yii\db\Query())->select('*')
                                                                    ->from("sys_rrhh_empleados_permisos pemp")
                                                                    ->innerjoin("sys_rrhh_permisos p", "pemp.id_sys_rrhh_permiso = p.id_sys_rrhh_permiso")
                                                                    ->innerjoin("sys_empresa", "pemp.id_sys_empresa = sys_empresa.id_sys_empresa")
                                                                    ->where("'{$fechaini}' >= fecha_ini")
                                                                    ->andwhere("'{$fechaini}' <= fecha_fin")
                                                                    ->andwhere("id_sys_rrhh_cedula like '%{$data['id_sys_rrhh_cedula']}%'")
                                                                    ->orderby("nivel")
                                                                    ->all(SysRrhhEmpleadosPermisos::getDb());
                                                                   
                                                                    if (count($permiso) > 0) :
                                                                        $cont++;
                                                                       $observacion = trim($permiso[0]['comentario']);
                                                                  ?>
                                                                        <tr>
                                                                               <td><?= $cont ?></td>
                                                                               <td><?= $data['nombres'] ?></td>
                                                                               <td><?= $entrada?></td>
                                                                               <td><?= $salida?></td>
                                                                               <td><?= $thoras?></td>
                                                                               <td><?= $atraso?></td>
                                                                               <td><?= $saltemp?></td>
                                                                               <td><?= $h25 ?></td>
                                                                               <td><?= $h50 ?></td>
                                                                               <td><?= $h100 ?></td>
                                                                               <td><?= $observacion?></td>
                                                                          </tr>
                                                     <?php           endif;
                                                              endif;
                                                    else:
                                                       //si tiene marcaciones
                                                         if($tipo == 'M'):
                                                              $cont++;
                                                              if(count($modelmarcacion) == 1 || count($modelmarcacion) == 2 ):
                                                         
                                                                 $fecha     =  $modelmarcacion[0]['fecha_jornada']; //fecha marcacion
                                                                 $tipojorna =  ObtenerTipoJornada('001', $data['id_sys_rrhh_cedula'], $fechaini); //Tipo de Jornada
                                                                 
                                                                 foreach ($modelmarcacion as $marcacion):
                                                                 
                                                                         if($marcacion['tipo']== 'E') :
                                                                         
                                                                             $fecha_ent = $marcacion['fecha_sistema'];
                                                                             $entrada   =  date('Y-m-d H:i:s', strtotime($fecha_ent));
                                                                             
                                                                         else:
                                                                         
                                                                             $fecha_sal = $marcacion['fecha_sistema'];
                                                                             $salida    = date('Y-m-d H:i:s', strtotime($fecha_sal));
                                                                             
                                                                         endif;
                                                                 
                                                                 endforeach;
                                                         
                                                         if(count($modelmarcacion) == 2):
                                                                 
                                                                 $totalemp++;
                                                                 
                                                                 $thoras        = getTotalhoras($fecha_ent, $fecha_sal);
                                                                 $totahorastrab = suma_horas($totahorastrab, $thoras); //horas laboradass
                                                                 
                                                                 $h25           = getRendonminutos(gethoras25 ($fecha_ent, $fecha_sal,$data['id_sys_rrhh_cedula'], $fecha));
                                                                 $totalh25      = suma_horas($totalh25, $h25);
                                                                 
                                                                 $h50           = getRendonminutos(gethoras50 ($fecha_ent, $fecha_sal,$data['id_sys_rrhh_cedula'], $fecha));
                                                                 $totalh50      = suma_horas($totalh50, $h50);
                                                                 
                                                                 $h100          = getRendonminutos(gethoras100($fecha_ent, $fecha_sal,$data['id_sys_rrhh_cedula'], $fecha, $tipojorna));
                                                                 $totalh100     = suma_horas($totalh100, $h100);
                                                                 
                                                                 
                                                             if($tipojorna == 'N'):
                                                             //buscar jornada normal
                                                                 
                                                                 if($dia >= 1 &&  $dia <= 5):
                                                                 
                                                                     $jornadaentra =  getInicioJornada('N',$data['id_sys_rrhh_cedula'], $entrada);
                                                                     
                                                                     if($entrada > $jornadaentra):
                                                                             
                                                                             $atraso = getTotalhoras($jornadaentra, $entrada);
                                                                             $totalatraso = suma_horas($totalatraso, $atraso);
                                                                             
                                                                     endif;
                                                                 
                                                                 endif;
                                                             
                                                             else:
                                                             
                                                                 $jornadaentra =  getInicioJornada('R',$data['id_sys_rrhh_cedula'], $entrada);
                                                             
                                                                 if($entrada > $jornadaentra):
                                                                 
                                                                     $atraso      = getTotalhoras($jornadaentra, $entrada);
                                                                     $totalatraso = suma_horas($totalatraso, $atraso);
                                                                     
                                                                 endif;
                                                                 
                                                             endif;
                                                         
                                                         endif;
                                                         
                                                  
                                                             //horas laborales
                                                             $jornadasal  = $fecha.' '.getTotalhoras($fecha_ent, $fecha_sal);
                                                             $horasnormal = $fecha.' '.getHorasJornada($data['id_sys_rrhh_cedula'], $fecha, $entrada, $salida); //horas jornadas
                                                             // $horasnormal = ($data['id_sys_rrhh_cedula'], $fecha, $entrada, $salida); //horas jornadas
                                                             
                                                             if($jornadasal < $horasnormal):
                                                             $saltemp      =  getTotalhoras($jornadasal, $horasnormal);
                                                             $totalsaltemp =  suma_horas($totalsaltemp, $saltemp);
                                                             endif;
                                                         
                                                                ?>
                                                                <tr>
                                                                   <td><?= $cont ?></td>
                                                                   <td><?= $data['nombres'] ?></td>
                                                                   <td><?= date('H:i:s', strtotime($entrada))?></td>
                                                                   <td><?= date('H:i:s', strtotime($salida)) ?></td>
                                                                   <td><?= $thoras?></td>
                                                                   <td><?= $atraso?></td>
                                                                   <td><?= $saltemp?></td>
                                                                   <td><?= $h25 ?></td>
                                                                   <td><?= $h50 ?></td>
                                                                   <td><?= $h100 ?></td>
                                                                   <td><?= $observacion?></td>
                                                                 </tr>
                                                               <?php
                                                              endif;
                                                        endif;
                                                 endif; 
                                           endif;
                                    endif;
                          endforeach;
                  endforeach;
              $fechaini = date("Y-m-d", strtotime($fechaini . " + 1 day"));
            }   
          ?>
       <?php if($tipo == 'M'): ?>	
            <tr>
               <td colspan ="4">Total de empleados que registraron entrada y salida <?= $totalemp?>:</td> 
               <td><?= $totahorastrab?></td>
               <td><?= $totalatraso?></td>
               <td><?= $totalsaltemp?></td>
               <td><?= $totalh25 ?></td>
               <td><?= $totalh50 ?></td>
               <td><?= $totalh100 ?></td>
               <td><?= $observacion?></td>
            </tr>
      <?php endif;?> 
    </tbody>
  </table>
 </div>







