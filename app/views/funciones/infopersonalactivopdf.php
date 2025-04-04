<?php
/* @var $this yii\web\View */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

?>

<?php if($datos):
        

       $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Listado de Empleados Activos";
        
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

        $hojita->setCellValue('A4', "Area");
        $hojita->getStyle('A4')->getFont()->setSize(12);
   
        $hojita->setCellValue('B4', "Departamento");
        $hojita->getStyle('B4')->getFont()->setSize(12);
       
        $hojita->setCellValue('C4', "Cédula");
        $hojita->getStyle('C4')->getFont()->setSize(12);
        
        $hojita->setCellValue('D4', "Nombres");
        $hojita->getStyle('D4')->getFont()->setSize(12);
        
        $hojita->setCellValue('E4', "Cargo");
        $hojita->getStyle('E4')->getFont()->setSize(12);
    
        $hojita->setCellValue('F4', "Formación Acádemica");
        $hojita->getStyle('F4')->getFont()->setSize(12);
        
        $hojita->setCellValue('G4', "Titulo Acádemico");
        $hojita->getStyle('G4')->getFont()->setSize(12);
        
        $hojita->setCellValue('H4', "Fecha Ingreso");
        $hojita->getStyle('H4')->getFont()->setSize(12);
        
        $hojita->setCellValue('I4', "Fecha Salida");
        $hojita->getStyle('I4')->getFont()->setSize(12);
        
        $hojita->setCellValue('J4', "Fecha Nacimiento");
        $hojita->getStyle('J4')->getFont()->setSize(12);
       
        $hojita->setCellValue('K4', "Edad");
        $hojita->getStyle('K4')->getFont()->setSize(12);
        
        $hojita->setCellValue('L4', "Estado Civil");
        $hojita->getStyle('L4')->getFont()->setSize(12);
        
        $hojita->setCellValue('N4', "Género");
        $hojita->getStyle('N4')->getFont()->setSize(12);
        
        $hojita->setCellValue('M4', "Teléfono");
        $hojita->getStyle('M4')->getFont()->setSize(12);
        
        $hojita->setCellValue('O4', "Celular");
        $hojita->getStyle('O4')->getFont()->setSize(12);
        
        $hojita->setCellValue('P4', "Parroquia");
        $hojita->getStyle('P4')->getFont()->setSize(12);
        
        $hojita->setCellValue('Q4', "Ciudad");
        $hojita->getStyle('Q4')->getFont()->setSize(12);
        
        $hojita->setCellValue('R4', "Provincia");
        $hojita->getStyle('R4')->getFont()->setSize(12);
        
        $hojita->setCellValue('S4', "Direccion");
        $hojita->getStyle('S4')->getFont()->setSize(12);
        
        
        $hojita->setCellValue('T4', 'Email');
        $hojita->getStyle('T4')->getFont()->setSize(12);

        $hojita->setCellValue('U4', "Sueldo");
        $hojita->getStyle('U4')->getFont()->setSize(12);
        
        $hojita->setCellValue('V4', "Contrato");
        $hojita->getStyle('V4')->getFont()->setSize(12);
        
        
        $hojita->setCellValue('W4', "No.Cuenta Bancaria");
        $hojita->getStyle('W4')->getFont()->setSize(12);
        
        
        $hojita->setCellValue('X4', "Centro de Costos");
        $hojita->getStyle('X4')->getFont()->setSize(12);
        
        
        
        $hojita->getStyle('A4:X4')->getFont()->setBold(true);
        $hojita->getStyle('A4:X4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setAutoFilter("A4:X4");
        
        $i = 4;
        
        
        foreach ($datos  as $data)
        {
            $i++;
            $hojita->setCellValue('A'.$i,  $data['area']);
            $hojita->setCellValue('B'.$i,  $data['departamento']);
            $hojita->setCellValue('C'.$i,  $data['id_sys_rrhh_cedula']);
            $hojita->setCellValue('D'.$i,  $data['nombres']);
            $hojita->setCellValue('E'.$i,  $data['cargo']);
            $hojita->setCellValue('F'.$i,  $data['formacion_academica']);
            $hojita->setCellValue('G'.$i,  $data['titulo_academico'] != '' ? $data['titulo_academico'] : 'NINGUNO');
            $hojita->setCellValue('H'.$i,  $data['fecha_ingreso']);
            $hojita->setCellValue('I'.$i,  $data['fecha_salida']);
            $hojita->setCellValue('J'.$i,  $data['fecha_nacimiento']);
            $hojita->setCellValue('K'.$i,  $data['edad'].' Años');
            $hojita->setCellValue('L'.$i,  $data['estado_civil']);
            $hojita->setCellValue('N'.$i,  $data['genero'] == 'M' ? 'Masculino': 'Femenino');
            $hojita->setCellValue('M'.$i,  $data['telefono']);
            $hojita->setCellValue('O'.$i,  $data['celular']);
            $hojita->setCellValue('P'.$i,  $data['parroquia']);
            $hojita->setCellValue('Q'.$i,  $data['canton']);
            $hojita->setCellValue('R'.$i,  $data['provincia']);
            $hojita->setCellValue('S'.$i,  $data['direccion']);
            $hojita->setCellValue('T'.$i,  $data['email']);
            $hojita->setCellValue('U'.$i,  $data['sueldo']);
            $hojita->setCellValue('V'.$i,  $data['contrato']);
            $hojita->setCellValue('W'.$i,  $data['cta_banco']);
            $hojita->setCellValue('X'.$i,  $data['id_sys_adm_ccosto']);
           
        }
        
        $hojita->getStyle('U5:U'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
        $hojita->getStyle('U5:U'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        
       
        
        foreach(range('A','X') as $columnID) {
            $hojita->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
        
        $nombreArchivo='Empleados_Activos.xlsx';
        
        $writer = new Xlsx($objPHPExcel);
        $writer->save($nombreArchivo);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
        exit;


endif;


//calcula Edad
    function calcularEdad($fechanacimiento){
    
        if($fechanacimiento != ''){
        
        $array  =  explode('-', $fechanacimiento);
        $anio   =  date('Y');
        $mes    =  date('n');
        $dia    =  date('j');
        
        $anios = ($anio - intval($array[0])) - 1;
        
        if($mes >= intval($array[1])){
            
            if($dia >= intval($array[2]))
            {
                $anios ++;
            };
            
        }
        
        return $anios.' Años.';
        
    }
    return '0 Años';
}

?> 





