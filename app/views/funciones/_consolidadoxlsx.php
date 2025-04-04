<?php
/* @var $this yii\web\View */

use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosNovedades;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysAdmCcostos;
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


$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];


$tipo = '';

if($periodo == 1 ):

$tipo = 'Quincenal';

elseif($periodo == 2 ) :

$tipo = 'Mensual';

elseif($periodo ==  90):

$tipo = 'Beneficios';

elseif($periodo == 71):

$tipo = 'Dec. Tercero';

elseif($periodo == 72):

$tipo = 'Dec. Cuarto';

endif;

$dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));

$dia = $dia > 30 ? '30': $dia;


 if($datos):
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= "CONSOLIDADO_PERIODO_".$periodo;
        
        $objPHPExcel->getProperties()
        ->setCreator("Gestion")
        ->setLastModifiedBy("Gestion")
        ->setTitle($titulo)
        ->setSubject($titulo);
        
        
        $hojita = $objPHPExcel->getActiveSheet();
        $hojita->setTitle($titulo);
        $hojita->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $hojita->getPageMargins()->setRight(0.30);
        $hojita->getPageMargins()->setLeft(0.30);
        
        $imagenLogo = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $imagenLogo->setName('Logo');
        $imagenLogo->setDescription('pespesca');
      //  $imagenLogo->setPath('logo/1391744064001/logo_reporte.jpg');
        $imagenLogo->setPath('logo/'.$empresa->ruc.'/'.$empresa->logo);
        $imagenLogo->setCoordinates('E1');
        $imagenLogo->setWidthAndHeight(250,250);
        $imagenLogo->setWorksheet($hojita);
        
        $hojita->setCellValue('A4', "Año ".$anio);
        $hojita->getStyle('A4')->getFont()->setSize(12);
        $hojita->getStyle('A4')->getFont()->setBold(true);
        $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
  
        $hojita->setCellValue('A5', "Mes ".$meses[$mes]);
        $hojita->getStyle('A5')->getFont()->setSize(12);
        $hojita->getStyle('A5')->getFont()->setBold(true);
        $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D4', "Tipo Rol ".$tipo);
        $hojita->getStyle('D4')->getFont()->setSize(12);
        $hojita->getStyle('D4')->getFont()->setBold(true);
        $hojita->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
       
        $hojita->setCellValue('D5', "Fecha Inicio:  01/".$mes.'/'.$anio);
        $hojita->getStyle('D5')->getFont()->setSize(12);
        $hojita->getStyle('D5')->getFont()->setBold(true);
        $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        
        
        $hojita->setCellValue('I4', "Fecha fin:  ".$dia."/".$mes."/".$anio);
        $hojita->getStyle('I4')->getFont()->setSize(12);
        $hojita->getStyle('I4')->getFont()->setBold(true);
        $hojita->getStyle('I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
      
        $listhaberes      =  ListHaberes($anio, $mes, $periodo, $area,  $departamento);
        $listdescuentos   =  ListDescuentos($anio, $mes, $periodo, $area, $departamento);   
 
         $hojita->setCellValue('A7', "Detalle");
       //  $hojita->getColumnDimensionByColumn('A')->setAutoSize(true);
         $hojita->setCellValue('B7', "Area");
        // $hojita->getColumnDimensionByColumn('B')->setAutoSize(true);
         $hojita->setCellValue('C7', "Descripcion");
        // $hojita->getColumnDimensionByColumn('C')->setAutoSize(true);
         $hojita->setCellValue('D7', "No.Empleados");
         //$hojita->getColumnDimensionByColumn('D')->setAutoSize(true);
         
         $i = 68;
         $j = 64;
         $k = 7;
         
         foreach ($listhaberes as $haber){
             $i++;
             $hojita->setCellValue(chr($i)."7", strtolower(str_replace("_", " ", $haber['id_sys_rrhh_concepto'] )));
            // $hojita->getColumnDimensionByColumn(chr($i))->setAutoSize(true);
          }
          
             $i++;
             
             $mjs =  $periodo != 90 ? 'Haberes': 'Proviciones';
             
             $hojita->setCellValue(chr($i)."7","Total ".$mjs);
           //  $hojita->getColumnDimensionByColumn(chr($i))->setAutoSize(true);
         
         if($listdescuentos > 0):
            
            foreach ($listdescuentos as $descuento){
             
                $i++;
                
                 if($i < 91):
                 
                    
                     $hojita->setCellValue(chr($i)."7", strtolower(str_replace("_", " ", $descuento['id_sys_rrhh_concepto'] )));
                     
                 else:
                 
                     $j++;
                     $hojita->setCellValue(chr(65)."".chr($j)."7", strtolower(str_replace("_", " ", $descuento['id_sys_rrhh_concepto'] )));
                     
                 endif;
                 
             }
             
             
             if($i < 91):
             
                 $i++;
                 $mjs =  $periodo != 90 ? 'Descuentos': 'Beneficios';
                 $hojita->setCellValue(chr($i)."7","Total ".$mjs);
             
             else:
                 $j++;
                 $hojita->setCellValue(chr(65)."".chr($j)."7", "Total a Pagar");
             
             endif;

             if($i < 91):
             
                 $i++;
                 $hojita->setCellValue(chr($i)."7","Total Pagar");
                 
             else:
            
                 $j++;
                 $hojita->setCellValue(chr(65)."".chr($j)."7", "Total a pagar");
             
             endif;
           
         else:
             $i++;
             $hojita->setCellValue(chr($i)."7","Total a Pagar");
         
         endif;
      
        
         if($i< 91):
         
             $hojita->getStyle('A7:'.chr($i).'7')->getFont()->setBold(true);
             $hojita->getStyle('A7:'.chr($i).'7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         
             
         else:
         
             $hojita->getStyle('A7:'.chr(65).''.chr($j).'7')->getFont()->setBold(true);
             $hojita->getStyle('A7:'.chr(65).''.chr($j).'7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
             
         
         endif;

         
         foreach ($datos as $index => $Id):
                   
              $k++;
              $hojita->setCellValue('A'.$k, $Id['area']);
              $hojita->setCellValue('B'.$k, $Id['id_sys_adm_ccosto']);
              $hojita->setCellValue('C'.$k, ObtenerAreaCcosto($Id['id_sys_adm_ccosto']));
              $hojita->setCellValue('D'.$k, $Id['numero']);
              
              $ii = 68;
              $jj = 64;
              $totalhab = 0;
              $totaldec = 0;
              foreach ($listhaberes as $haber):
              
                     $ii++;
                     $hojita->setCellValue(chr($ii)."".$k, floatval(ObtenerConcepto($anio, $mes, $periodo, $haber['id_sys_rrhh_concepto'], $Id['id_sys_adm_ccosto'])));
                     $valor = floatval(ObtenerConcepto($anio, $mes, $periodo, $haber['id_sys_rrhh_concepto'], $Id['id_sys_adm_ccosto']));
                     $totalhab = $totalhab + $valor;
                     
              endforeach;
              
              $ii++;
              $hojita->setCellValue(chr($ii)."".$k, $totalhab);
             
           
              $valor = 0;
              
              foreach ($listdescuentos as $descuento):
              
                    $ii++;
                    $valor    =  floatval(ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id['id_sys_adm_ccosto']));
                    $totaldec = $totaldec + $valor;
              
              
                     
                      if($ii < 91):
                           $hojita->setCellValue(chr($ii)."".$k,  floatval(ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id['id_sys_adm_ccosto'])));
                      else:
                          
                          $jj++;
                          $hojita->setCellValue(chr(65)."".chr($jj)."".$k, floatval(ObtenerConcepto($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $Id['id_sys_adm_ccosto'])));
                         
                      endif;
   
              endforeach;
              
                  if($ii< 91):
                      
                  
                      $ii++;
                      $hojita->setCellValue(chr($ii)."".$k,  floatval($totaldec));
                      $ii++;
                      
                      if($ii < 91):
                          
                          if($periodo != 90):
                      
                              $hojita->setCellValue(chr($ii)."".$k, floatval($totalhab - $totaldec));
                      
                            else:
                            
                              $hojita->setCellValue(chr($ii)."".$k, floatval($totalhab + $totaldec));
                            
                           endif;
                       else:
                       
                       
                           $jj++;
                          
                           if($periodo != 90):
                           
                              $hojita->setCellValue(chr(65)."".chr($jj)."".$k, floatval($totalhab - $totaldec));
                           
                           else:
                           
                              $hojita->setCellValue(chr(65)."".chr($jj)."".$k, floatval($totalhab + $totaldec));
                           
                           endif;
                        
                      endif;
     
                  else:
                  
                      $jj++;
                 
                      $hojita->setCellValue(chr(65)."".chr($jj)."".$k, $totaldec);
                 
                      $jj++;
                      
                      if($periodo != 90):
                      
                         $hojita->setCellValue(chr(65)."".chr($jj)."".$k,$totalhab - $totaldec);
                      
                      else:
                      
                          $hojita->setCellValue(chr(65)."".chr($jj)."".$k,$totalhab + $totaldec);
                      
                      endif;
                      
                  endif;
                 
         endforeach;?>
         
 

       <?php  
       
       if($ii < 91):
       
   
           $hojita->getStyle('E8:'.chr($ii).''.$k)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
           $hojita->getStyle('E8:'.chr($ii).''.$k)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
           
           
           
            $hojita->getStyle('E8:'.chr($ii).''.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hojita->getStyle('E8:'.chr($ii).''.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
           
           foreach(range('A', ''.chr($ii).'') as $columnID) {
               $hojita->getColumnDimension($columnID)->setAutoSize(true);
           }
           
           
 
       else:
  
           $hojita->getStyle('E8:'.chr(65)."".chr($jj).''.$k)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
           $hojita->getStyle('E8:'.chr(65)."".chr($jj).''.$k)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
           
           $hojita->getStyle('E8:'.chr(65)."".chr($jj).''.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
           $hojita->getStyle('E8:'.chr(65)."".chr($jj).''.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
          
           foreach(range('A', ''.chr($jj).'') as $columnID) {
               $hojita->getColumnDimension($columnID)->setAutoSize(true);
           }
           
           
       endif;
       
       
       $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
       

        $nombreArchivo=$titulo.'.xlsx';
        
        $writer = new Xlsx($objPHPExcel);
        $writer->save($nombreArchivo);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
        exit;
        
        
 endif;?>     

<?php 
function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
    $position = array();
    $newRow = array();
    foreach ($toOrderArray as $key => $row) {
        $position[$key]  = $row[$field];
        $newRow[$key] = $row;
    }
    //$position = array_unique($position);
    if ($inverse) {
        arsort($position);
    }
    else {
        asort($position);
    }
    $returnArray = array();
    foreach ($position as $key => $pos) {
        $returnArray[] = $newRow[$key];
    }
    
    return $returnArray;
}

function ObtenerHaberes($anio, $mes, $periodo, $area, $departamento){
    
    
   return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
      //  ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.tipo = 'I'")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andwhere("rol_mov.valor > 0")
        ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
        ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
        ->scalar(SysRrhhEmpleadosNovedades::getDb());
    
}

function ObtenerDescuentos($anio, $mes, $periodo, $area, $departamento){
    
    return   (new \yii\db\Query())->select('count(distinct rol_mov.id_sys_rrhh_concepto)')
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    //->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
   // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'E'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
}


function ListHaberes($anio, $mes, $periodo, $area, $departamento){
    
    return   (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto',  'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
   // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
   // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'I'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());
   
}

function ListDescuentos($anio, $mes, $periodo, $area, $departamento){
    
    return   (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
   // ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
   // ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'E'")
    ->andwhere("rol_mov.valor > 0")
    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());
}
function ObtenerConcepto($anio, $mes, $periodo, $concepto, $ccosto){
    
    return   (new \yii\db\Query())->select(['sum(rol_mov.valor)'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
    ->andwhere("emp.id_sys_adm_ccosto = '{$ccosto}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->scalar(SysRrhhEmpleadosNovedades::getDb());
   
}
function ObtenerAreaCcosto($id_sys_adm_ccosto){
       
    return   (new \yii\db\Query())->select(['centro_costo'])
    ->from("sys_adm_ccostos")
    ->where("id_sys_adm_ccosto = '{$id_sys_adm_ccosto}'")
    ->andwhere("id_sys_empresa =  '001'")
    ->scalar(SysAdmCcostos::getDb());
    
}



?>
