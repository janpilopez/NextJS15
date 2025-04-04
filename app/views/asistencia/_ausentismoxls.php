<?php
/* @var $this yii\web\View */


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhMarcacionesEmpleados;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


 /*if($datos): */
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Informe Ausentismo Laboral";
        
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
        
        $hojita->setCellValue('A4', "Ausentismo Laboral - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
        $hojita->getStyle('A4')->getFont()->setSize(15);
        $hojita->getStyle('A4')->getFont()->setBold(true);
        $hojita->mergeCells('A4:J4');
        $hojita->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('A6', "Area");
        $hojita->getStyle('A6')->getFont()->setSize(12);
        $hojita->getStyle('A6')->getFont()->setBold(true);
        $hojita->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('B6', "Departamento");
        $hojita->getStyle('B6')->getFont()->setSize(12);
        $hojita->getStyle('B6')->getFont()->setBold(true);
        $hojita->getStyle('B6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('C6', "Identificación");
        $hojita->getStyle('C6')->getFont()->setSize(12);
        $hojita->getStyle('C6')->getFont()->setBold(true);
        $hojita->getStyle('C6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('D6', "Nombres");
        $hojita->getStyle('D6')->getFont()->setSize(12);
        $hojita->getStyle('D6')->getFont()->setBold(true);
        $hojita->getStyle('D6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('E6', "Fecha");
        $hojita->getStyle('E6')->getFont()->setSize(12);
        $hojita->getStyle('E6')->getFont()->setBold(true);
        $hojita->getStyle('E6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $hojita->setCellValue('F6', "Tipo Ausentismo");
        $hojita->getStyle('F6')->getFont()->setSize(12);
        $hojita->getStyle('F6')->getFont()->setBold(true);
        $hojita->getStyle('F6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
         
        $hojita->setCellValue('G6', "Comentario");
        $hojita->getStyle('G6')->getFont()->setSize(12);
        $hojita->getStyle('G6')->getFont()->setBold(true);
        $hojita->getStyle('G6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
       
        $hojita->setAutoFilter("A6:G6");
        
        $i = 6;
        $ban = 0;
        $tipoAusentismo = '';
        $comentario = '';
        
        foreach ($empleados as $empleado):
        
            $fechafinal = $fechaini;
        
            while(strtotime($fechafinal) <= strtotime($fechafin))
            {
                
                    $ban = 0;
                    $tipoAusentismo = '';
                    $jornada =  ObtenerTipoJornada('001', $empleado['id_sys_rrhh_cedula'], $fechafinal);
                    $modelmarcacion = SysRrhhEmpleadosMarcacionesReloj::find()->where(['fecha_jornada'=> $fechafinal, 'id_sys_rrhh_cedula'=> $empleado['id_sys_rrhh_cedula']])->andWhere(['estado'=> 'A'])->all();
                    //obtenemos el numero de dia de la semana
                    $dia = date("N", strtotime($fechafinal));
                    
                    if(!$modelmarcacion):
                    
                        
                            if($empleado['fecha_salida'] == null || $empleado['fecha_salida'] <= $fechafinal):
                            
                                    
                                        $permiso =  getPermiso($fechafinal, $empleado['id_sys_rrhh_cedula']);
                                        //Valida Permiso 
                                        if($permiso):
                                        
                                        
                                           $tipoAusentismo = $permiso['permiso'];
                                           $comentario = $permiso['comentario'];
                                           $ban = 1;
                                        
                                        else:
                                             //Valida Vacaciones
                                             $vacaciones = getVacaciones($fechafinal, $empleado['id_sys_rrhh_cedula']);
                                        
                                             if($vacaciones):
                                                 
                                                  $tipoAusentismo = 'VACACIONES';
                                                  $comentario = 'GOZO DE VACACIONES';
                                                  $ban=1;
                                                  
                                             else:
                                                  //Valida Feriados
                                                  $feriado = getFeriado($fechafinal);
                                             
                                                  if($feriado):
                                                  
                                                      $tipoAusentismo = 'FERIADOS';
                                                      $comentario = $feriado['feriado'];
                                                      $ban =1;
                                                      
                                                  else:
                                                  
                                                            if($empleado['reg_ent_salida'] == 'S'):
                                                                
                                                                 if($empleado['fecha_ingreso'] >= $fechafinal && $empleado['fecha_ingreso'] <= $fechafinal):
                                                            
                                                                           //Tipo de jornada
                                                                           if($jornada == 'N'):
                                                                           
                                                                           //fines de Semanaa
                                                                                 if($dia < 6 ):
                                                                                 
                                                                                      $tipoAusentismo = 'FALTA';
                                                                                      $comentario = 'FALTA NO JUSTIFICADA';
                                                                                      $ban =1;
        
                                                                                  endif;
                                                                           
                                                                           else:
                                                                                   
                                                                                   $libre =  getLibre($empleado['id_sys_rrhh_cedula'], $fechafinal);
                                                                                   
                                                                                   if($libre == false):
                                                                                   
                                                                                       $tipoAusentismo = 'FALTA';
                                                                                       $comentario = 'FALTA NO JUSTIFICADA';
                                                                                       $ban = 1;
                                                                                       
                                                                                   else:
                                                                                       
                                                                                      $tipoAusentismo = 'DIA LIBRE';
                                                                                      $comentario = 'AGENDAMIENTO LABORAL';
                                                                                      $ban = 1;
                                                                                      
                                                                                   endif;
                                                                           
                                                                           endif;
                                                                           
                                                                    elseif($empleado['fecha_ingreso'] <= $fechafinal):
                                                                  
                                                                            //Tipo de jornada
                                                                            if($jornada == 'N'):
                                                                            
                                                                                //fines de Semanaa
                                                                                if($dia < 6 ):
                                                                                    
                                                                                    $tipoAusentismo = 'FALTA';
                                                                                    $comentario = 'FALTA NO JUSTIFICADA';
                                                                                    $ban =1;
                                                                                
                                                                                endif;
                                                                            
                                                                            else:
                                                                                
                                                                                $libre =  getLibre($empleado['id_sys_rrhh_cedula'], $fechafinal);
                                                                                
                                                                                if($libre == false):
                                                                                
                                                                                    
                                                                                    $tipoAusentismo = 'FALTA';
                                                                                    $comentario = 'FALTA NO JUSTIFICADA';
                                                                                    $ban = 1;
                                                                                    
                                                                                 else:    
                                                                                 
                                                                                     $tipoAusentismo = 'DIA LIBRE';
                                                                                     $comentario = 'AGENDAMIENTO LABORAL';
                                                                                     $ban = 1;

                                                                                 endif;
                                                                            
                                                                            endif;
  
                                                                  endif;  
                                                          endif;
                                                  endif;
                    
                                             endif;
                                             
                                        endif;
                                        
                           endif;
                                 
                    else:
                        $permiso =  getPermiso($fechafinal, $empleado['id_sys_rrhh_cedula']);
                        //Valida Permiso
                        if($permiso):    
                            $tipoAusentismo = $permiso['permiso'].'- Parcial';
                            $comentario = $permiso['comentario'];
                            $ban = 1;
                         endif;
                    endif;
                
                //Inserta Falta
                if($ban == 1):
                
                    $i++;
                    $hojita->setCellValue('A'.$i, $empleado['area']);
                    $hojita->setCellValue('B'.$i, $empleado['departamento']);
                    $hojita->setCellValue('C'.$i, $empleado['id_sys_rrhh_cedula']);
                    $hojita->setCellValue('D'.$i, $empleado['nombres']);
                    $hojita->setCellValue('E'.$i, date('d/m/Y',strtotime($fechafinal)));
                    $hojita->setCellValue('F'.$i, $tipoAusentismo);
                    $hojita->setCellValue('G'.$i, $comentario);
                    
                    
                endif;
                
                $fechafinal = date("Y-m-d", strtotime($fechafinal . " + 1 day"));
            }
    
        endforeach; 
        
        
        /*
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
        
        $hojita->setCellValue('A6', "Area");
        $hojita->getStyle('A6')->getFont()->setSize(12);
        
        $hojita->setCellValue('B6', "Departamento");
        $hojita->getStyle('B6')->getFont()->setSize(12);
        
        $hojita->setCellValue('C6', "Cédula");
        $hojita->getStyle('C6')->getFont()->setSize(12);
        
        $hojita->setCellValue('D6', "Nombres");
        $hojita->getStyle('D6')->getFont()->setSize(12);
        
        $hojita->setCellValue('E6', "Dias");
        $hojita->getStyle('E6')->getFont()->setSize(12);
        
        $hojita->setCellValue('F6', "H25");
        $hojita->getStyle('F6')->getFont()->setSize(12);
        
        $hojita->setCellValue('G6', "H50");
        $hojita->getStyle('G6')->getFont()->setSize(12);
        
        $hojita->setCellValue('H6', "H100");
        $hojita->getStyle('H6')->getFont()->setSize(12);
        
        $hojita->getStyle('A6:H6')->getFont()->stBold(true);
        $hojita->getStyle('A6:H6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setAutoFilter("A6:H6");
        
        $i = 6;
        
        
        foreach ($datos  as $data)
        {
            $i++;
            $hojita->setCellValue('A'.$i, $data['area']);
            $hojita->setCellValue('B'.$i, $data['departamento']);
            $hojita->setCellValue('C'.$i, $data['id_sys_rrhh_cedula']);
            $hojita->setCellValue('D'.$i, utf8_encode($data['nombres']));
            $hojita->setCellValue('E'.$i, $data['cantidad']);
            $hojita->setCellValue('F'.$i, $data['h25']);
            $hojita->setCellValue('G'.$i, $data['h50']);
            $hojita->setCellValue('H'.$i, $data['h100']);
       
            
        }
        
        $hojita->getStyle('F6:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('F6:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        $hojita->getStyle('G6:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('G6:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        $hojita->getStyle('H6:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('H6:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
        
        */
        
        foreach(range('A','G') as $columnID) {
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
    
/* endif;?>*/ 
