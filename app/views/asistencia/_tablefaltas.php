<?php 
use app\models\SysConfiguracion;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhFeriados;
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhMarcacionesEmpleados;
use yii\bootstrap\Html;
use SebastianBergmann\CodeCoverage\Report\PHP;
use app\models\SysRrhhEmpleados;
$holgura =  15;
$cont = 0;
$totahorastrab = '00:00:00';
$totalh25      = '00:00:00';
$totalh50      = '00:00:00';
$totalh100     = '00:00:00';
$totalatraso   = '00:00:00';
$totalsaltemp  = '00:00:00'; 
$totalemp      = 0; 
//listado de funciones de calculos
echo $this->render('funciones');
$dias = [1 => 'Lunes',  2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

?>
<table  class="table table-bordered table-condensed" style="background-color: white; font-size: 10px; width: 100%">
    <thead>
      <tr>
        <th>No</th>
        <th>Departamento</th>
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
        <tr>
           <th colspan = '12'><?= $fechaini .'-'.$dias[date("N", strtotime($fechaini))]?></th>
        </tr>
         
      <?php
         
     foreach ($datos as $data):
      //verificamos si tiene marcacion
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
                
       
          if($fechaini >= $fechaingresoemp['fecha_ingreso']):
       
       
                    $modelmarcacion = SysRrhhEmpleadosMarcacionesReloj::find()->where(['fecha_jornada'=> $fechaini, 'id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])->andWhere(['estado'=> 'A'])->all();
                
                    if(!$modelmarcacion):
                      
                             $observacion =   getJustificacion($fechaini, $data['id_sys_rrhh_cedula']);
                   
                             if($tipo == 'F' && $observacion == 'FALTA'):
                             $cont++;
                         ?> 
                                <tr>
                                       <td><?= $cont?></td>
                                       <td><?= $data['departamento'] ?></td>
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
                                       <td><?= $cont?></td>
                                       <td><?= $data['departamento'] ?></td>
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
                                      <td><?= $cont?></td>
                                       <td><?= $data['departamento'] ?></td>
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
                             $cont++;
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
                            
                               $observacion = trim($permiso[0]['comentario']);
                          ?>
                                <tr>  
                                      <td><?= $cont?></td>
                                       <td><?= $data['departamento'] ?></td>
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
                              
                        <?php endif;
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
                                           <td><?= $data['departamento'] ?></td>
                                           <td><?= $data['nombres'] ?></td>
                                           <td><?= date('H:i:s', strtotime($entrada)) ?></td>
                                           <td><?= date('H:i:s', strtotime($salida))?></td>
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
                 endif;//valida fecha ingreso
           endforeach;
        $fechaini = date("Y-m-d", strtotime($fechaini . " + 1 day"));
        
      }?>
      
      <?php if($tipo == 'M'): ?>	
        <tr>
           <td colspan ="5">Total de empleados que registraron entrada y salida <?= $totalemp?>:</td> 
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
 
