<?php
/* @var $this yii\web\View */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhEmpleados;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

echo $this->render('funciones');
?>

<?php if($datos):
        

  $objPHPExcel =  new Spreadsheet();

   $titulo= "Listado de Horas Extras Detalle";
   
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
   $imagenLogo->setCoordinates('B1');
   $imagenLogo->setWidthAndHeight(250,250);
   $imagenLogo->setWorksheet($hojita);

   $hojita->setCellValue('D2', "Detalle Horas Extras - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:O2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

   $hojita->setCellValue('A4', "Fecha");
   $hojita->getStyle('A4')->getFont()->setSize(12);

   $hojita->setCellValue('B4', "Area");
   $hojita->getStyle('B4')->getFont()->setSize(12);
  
   $hojita->setCellValue('C4', "Departamento");
   $hojita->getStyle('C4')->getFont()->setSize(12);
   
   $hojita->setCellValue('D4', "Identificación");
   $hojita->getStyle('D4')->getFont()->setSize(12);
   
   $hojita->setCellValue('E4', "Nombres");
   $hojita->getStyle('E4')->getFont()->setSize(12);

   $hojita->setCellValue('F4', "(H)25");
   $hojita->getStyle('F4')->getFont()->setSize(12);
   
   $hojita->setCellValue('G4', "($)25");
   $hojita->getStyle('G4')->getFont()->setSize(12);
   
   $hojita->setCellValue('H4', "(H)50");
   $hojita->getStyle('H4')->getFont()->setSize(12);
   
   $hojita->setCellValue('I4', "($)50");
   $hojita->getStyle('I4')->getFont()->setSize(12);
   
   $hojita->setCellValue('J4', "(H)100");
   $hojita->getStyle('J4')->getFont()->setSize(12);
  
   $hojita->setCellValue('K4', "($)100");
   $hojita->getStyle('K4')->getFont()->setSize(12);
   
   $hojita->getStyle('A4:K4')->getFont()->setBold(true);
   $hojita->getStyle('A4:K4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
   
   $hojita->setAutoFilter("A4:K4");
   
   $i = 4;
    $totalh25 = 0;
    $totalv25 = 0;
    $totalh50 = 0;
    $totalv50 = 0;
    $totalh100 = 0;
    $totalv100 = 0;
   
   foreach ($datos  as $data)
   {
    $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
    ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
    ->andWhere(['estado'=> 'A'])
    ->scalar();

    $contratoemp  =  SysRrhhEmpleadosContratos::find()
    ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
    ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
    ->one();

    $empleado     = SysRrhhEmpleados::find()
    ->where(['id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula']])
    ->one();

    $cargoemp     =  SysAdmCargos::find()
    ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
    ->one();
  
    if($contratoemp->fecha_salida == null || date('n',strtotime($contratoemp->fecha_salida)) == date('n',strtotime($fechafin)) || $contratoemp->fecha_salida > $fechafin):
      
      if($cargoemp->reg_horas_extras == 'S'):
        

        $valorhora    =  floatval($sueldoemp/240);

        $val25 = floatval(($valorhora * 0.25) * $data['h25']);

        $newvalor = floatval(($valorhora * 0.50)) + $valorhora;
                  
        $val50  = floatval($newvalor * floatval($data['h50']));

        $val100   = floatval(($valorhora * 2) * $data['h100']);


        $totalv25  += number_format($val25, 2, '.', '');
        $totalv50  += number_format($val50, 2, '.', '');
        $totalv100 += number_format($val100, 2, '.', '');

        $totalh25  += $data['h25'];
        $totalh50  += $data['h50'];
        $totalh100 += $data['h100'];
       
        $i++;
       $hojita->setCellValue('A'.$i,  $data['fecha']);
       $hojita->setCellValue('B'.$i,  $data['area']);
       $hojita->setCellValue('C'.$i,  $data['departamento']);
       $hojita->setCellValue('D'.$i,  $data['id_sys_rrhh_cedula']);
       $hojita->setCellValue('E'.$i,  $data['nombres']);
       $hojita->setCellValue('F'.$i,  DecimaltoHoras(number_format($data['h25'], 2, '.', '')));
       $hojita->setCellValue('G'.$i,  $val25 != 0 ? number_format($val25, 2, '.', '') : '.00');
       $hojita->setCellValue('H'.$i,  DecimaltoHoras(number_format($data['h50'], 2, '.', '')));
       $hojita->setCellValue('I'.$i,  $val50 != 0 ? number_format($val50, 2, '.', '') : '.00');
       $hojita->setCellValue('J'.$i,  DecimaltoHoras(number_format($data['h100'], 2, '.', '')));
       $hojita->setCellValue('K'.$i,  $val100 != 0 ? number_format($val100, 2, '.', '') : '.00');

      endif;
    endif;
   }
   
   $hojita->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

   $hojita->getStyle('I5:I'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('I5:I'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

   $hojita->getStyle('K5:K'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('K5:K'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
   
  
   
   foreach(range('A','K') as $columnID) {
       $hojita->getColumnDimension($columnID)->setAutoSize(true);
   }
   
   $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
   
   $nombreArchivo='Horas_Extras_Detalle de '.$fechaini.' hasta '.$fechafin.'.xlsx';
   
   $writer = new Xlsx($objPHPExcel);
   $writer->save($nombreArchivo);
   $objPHPExcel->disconnectWorksheets();
   unset($objPHPExcel);
   header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
   exit;


endif;
?>