<?php
/* @var $this yii\web\View */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhEmpleados;
use app\models\SysAdmDepartamentos;
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

echo $this->render('funciones');

class FilterColumn {
  private $colName;
  
  function __construct($colName) {
      $this->colName = $colName;
  }
  
  function getValues($i) {
      
      return $i[$this->colName] ;
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

<?php if($datos):
        

  $objPHPExcel =  new Spreadsheet();

   $titulo= "Listado de Horas Extras Resumen";
   
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

   $hojita->setCellValue('D2', "Resumen Horas Extras - Desde ".date('d/m/Y', strtotime($fechaini))." Hasta ".date('d/m/Y', strtotime($fechafin)));
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:O2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

   $hojita->setCellValue('A4', "Area");
   $hojita->getStyle('A4')->getFont()->setSize(12);
  
   $hojita->setCellValue('B4', "Departamento");
   $hojita->getStyle('B4')->getFont()->setSize(12);

   $hojita->setCellValue('C4', "(H)25");
   $hojita->getStyle('C4')->getFont()->setSize(12);
   
   $hojita->setCellValue('D4', "($)25");
   $hojita->getStyle('D4')->getFont()->setSize(12);
   
   $hojita->setCellValue('E4', "(H)50");
   $hojita->getStyle('E4')->getFont()->setSize(12);
   
   $hojita->setCellValue('F4', "($)50");
   $hojita->getStyle('F4')->getFont()->setSize(12);
   
   $hojita->setCellValue('G4', "(H)100");
   $hojita->getStyle('G4')->getFont()->setSize(12);
  
   $hojita->setCellValue('H4', "($)100");
   $hojita->getStyle('H4')->getFont()->setSize(12);
   
   $hojita->getStyle('A4:H4')->getFont()->setBold(true);
   $hojita->getStyle('A4:H4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
   
   $hojita->setAutoFilter("A4:H4");
   
   $i = 4;

   $dataFilterIdSysAdmArea =  array_unique(array_map(array(new FilterColumn("departamento"), 'getValues'), $datos));    
            
      $con = 0;
      foreach ($dataFilterIdSysAdmArea as $index => $departamento):  
        $con+=1;
        $area = '';
        $nombres = '';
        $arrayData   = array_filter($datos, array(new FilterData("departamento", $departamento), 'getFilter'));
        $horas = '';
        $horasDecimal  = 0;
        $h25 = 0;
        $h50 = 0;
        $h100 = 0;
        $tv25 = 0;
        $tv50 = 0;
        $tv100 = 0;
        $totalh25 = 0;
        $totalh50 = 0;
        $totalh100 = 0;
        $valorhora = 0;
        
        foreach ($arrayData as $index => $row):
                  
          $area = $row['area'];
          $deparamento = $row['departamento'];
          $h25 += $row['h25'];
          $h50 += $row['h50'];
          $h100 += $row['h100'];
                
        endforeach;

        $totalv25 = 0;
        $totalv50 = 0;
        $totalv100 = 0;
        $value25 = 0;
        $value50 = 0;
        $value100 = 0;

        foreach ($datos as $index  => $data):

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
          
          $departamento = SysAdmDepartamentos::find()
          ->where(['departamento' => $deparamento])
          ->one();

          if($contratoemp->fecha_salida == null || date('n',strtotime($contratoemp->fecha_salida)) == date('n',strtotime($fechafin)) || $contratoemp->fecha_salida > $fechafin):
        
            if($cargoemp->reg_horas_extras == 'S'):

              $valorhora    =  floatval($sueldoemp/240);

              $val25 = floatval(($valorhora * 0.25) * $data['h25']);

              $newvalor = floatval(($valorhora * 0.50)) + $valorhora;
                      
              $val50  = floatval($newvalor * floatval($data['h50']));

              $val100   = floatval(($valorhora * 2) * $data['h100']);

              if($cargoemp->id_sys_adm_departamento == $departamento->id_sys_adm_departamento):

                $value25  += number_format($val25, 2, '.', '');
                $value50  += number_format($val50, 2, '.', '');
                $value100 += number_format($val100, 2, '.', ''); 

              endif;

              $totalv25  += number_format($val25, 2, '.', '');
              $totalv50  += number_format($val50, 2, '.', '');
              $totalv100 += number_format($val100, 2, '.', ''); 
        
              $totalh25  += $data['h25'];
              $totalh50  += $data['h50'];
              $totalh100 += $data['h100'];

            endif;
          
          endif;

        endforeach;

        $i++;
        $hojita->setCellValue('A'.$i, $area);
        $hojita->setCellValue('B'.$i, $deparamento);
        $hojita->setCellValue('C'.$i, DecimaltoHoras(number_format($h25, 2, '.', '')));
        $hojita->setCellValue('D'.$i, $value25 != 0 ? number_format($value25, 2, '.', '') : '.00');
        $hojita->setCellValue('E'.$i, DecimaltoHoras(number_format($h50, 2, '.', '')));
        $hojita->setCellValue('F'.$i, $value50 != 0 ? number_format($value50, 2, '.', '') : '.00');
        $hojita->setCellValue('G'.$i, DecimaltoHoras(number_format($h100, 2, '.', '')));
        $hojita->setCellValue('H'.$i, $value100 != 0 ? number_format($value100, 2, '.', '') : '.00');
      endforeach;
   
   
   $hojita->getStyle('D5:D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('D5:D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

   $hojita->getStyle('F5:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('F5:F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

   $hojita->getStyle('H5:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
   $hojita->getStyle('H5:H'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
   
   foreach(range('A','H') as $columnID) {
       $hojita->getColumnDimension($columnID)->setAutoSize(true);
   }
   
   $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
   
   $nombreArchivo='Horas_Extras_Resumen_Area_Departamento de '.$fechaini.' hasta '.$fechafin.'.xlsx';
   
   $writer = new Xlsx($objPHPExcel);
   $writer->save($nombreArchivo);
   $objPHPExcel->disconnectWorksheets();
   unset($objPHPExcel);
   header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
   exit;


endif;
?>