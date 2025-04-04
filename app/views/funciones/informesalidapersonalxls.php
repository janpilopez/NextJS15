<?php
/* @var $this yii\web\View */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhMarcacionesEmpleados;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic' ];

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


 /*if($datos): */
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Salida Personal ".$anio." Mes ".$meses[$mesini];
        
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
        $imagenLogo->setPath('logo/'.$empresa->ruc.'/'.$empresa->logo);
        $imagenLogo->setCoordinates('A1');
        $imagenLogo->setWidthAndHeight(250,250);
        $imagenLogo->setWorksheet($hojita);
        
        $hojita->setCellValue('C2', "Informe Salida de Personal Año ".$anio." Mes Inicio ".$meses[$mesini]." Mes Final ".$meses[$mesfin]);
        $hojita->getStyle('C2')->getFont()->setSize(15);
        $hojita->getStyle('C2')->getFont()->setBold(true);
        $hojita->mergeCells('C2:H2');
        $hojita->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('A5', "Cédula");
        $hojita->getStyle('A5')->getFont()->setSize(12);
        $hojita->getStyle('A5')->getFont()->setBold(true);
        $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('B5', "Apellidos y Nombres");
        $hojita->getStyle('B5')->getFont()->setSize(12);
        $hojita->getStyle('B5')->getFont()->setBold(true);
        $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('C5', "Área");
        $hojita->getStyle('C5')->getFont()->setSize(12);
        $hojita->getStyle('C5')->getFont()->setBold(true);
        $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D5', "Departamento");
        $hojita->getStyle('D5')->getFont()->setSize(12);
        $hojita->getStyle('D5')->getFont()->setBold(true);
        $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('E5', "Motivo de Salida");
        $hojita->getStyle('E5')->getFont()->setSize(12);
        $hojita->getStyle('E5')->getFont()->setBold(true);
        $hojita->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $hojita->setCellValue('F5', "Fecha Ingreso");
        $hojita->getStyle('F5')->getFont()->setSize(12);
        $hojita->getStyle('F5')->getFont()->setBold(true);
        $hojita->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('G5', "Fecha Salida");
        $hojita->getStyle('G5')->getFont()->setSize(12);
        $hojita->getStyle('G5')->getFont()->setBold(true);
        $hojita->getStyle('G5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('H5', "Género");
        $hojita->getStyle('H5')->getFont()->setSize(12);
        $hojita->getStyle('H5')->getFont()->setBold(true);
        $hojita->getStyle('H5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $hojita->setCellValue('I5', "Tiempo Elaborado");
        $hojita->getStyle('I5')->getFont()->setSize(12);
        $hojita->getStyle('I5')->getFont()->setBold(true);
        $hojita->getStyle('I5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
       
        $hojita->setAutoFilter("A5:I5");
        
        $i = 5;

        if ($datos):

            foreach ($datos as $row){
                $i++;
                $hojita->setCellValue('A'.$i,  $row['id_sys_rrhh_cedula']);
                $hojita->setCellValue('B'.$i,  $row['nombres']);
                $hojita->setCellValue('C'.$i,  $row['area']);
                $hojita->setCellValue('D'.$i,  $row['departamento']);
                $hojita->setCellValue('E'.$i,  $row['descripcion']);
                $hojita->setCellValue('F'.$i,  $row['fecha_ingreso']);
                $hojita->setCellValue('G'.$i,  $row['fecha_salida']);
                $hojita->setCellValue('H'.$i,  $row['genero']);
                $hojita->setCellValue('I'.$i, tiempoTranscurridoFechas($row['fecha_ingreso'], $row['fecha_salida']));
            }

        endif;
        
        
        foreach(range('A','I') as $columnID) {
            $hojita->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
        $nombreArchivo=$titulo.'.xlsx';
        $writer = new Xlsx($objPHPExcel);
        $writer->save($nombreArchivo);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
        exit;
    


        function tiempoTranscurridoFechas($fechaInicio,$fechaFin)
          {
          $fecha1 = new DateTime($fechaInicio);
          $fecha2 = new DateTime($fechaFin);
          $fecha = $fecha1->diff($fecha2);
          $tiempo = "";
          
          //años
          if($fecha->y > 0)
          {
               $tiempo .= $fecha->y;
               
               if($fecha->y == 1)
                    $tiempo .= " año, ";
                    else
                         $tiempo .= " años, ";
          }
          
          //meses
          if($fecha->m > 0)
          {
               $tiempo .= $fecha->m;
               
               if($fecha->m == 1)
                    $tiempo .= " mes, ";
                    else
                         $tiempo .= " meses, ";
          }
          
          //dias
          if($fecha->d > 0)
          {
               $tiempo .= $fecha->d;
               
               if($fecha->d == 1)
                    $tiempo .= " día, ";
                    else
                         $tiempo .= " días, ";
          }
          
          //horas
          if($fecha->h > 0)
          {
               $tiempo .= $fecha->h;
               
               if($fecha->h == 1)
                    $tiempo .= " hora, ";
                    else
                         $tiempo .= " horas, ";
          }
          
               return $tiempo;
          }

/* endif;?>*/ 
