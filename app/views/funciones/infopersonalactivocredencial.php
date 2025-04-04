<?php
/* @var $this yii\web\View */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

?>

<?php if($datos):
        

       $objPHPExcel =  new Spreadsheet();
 
        $titulo= "Datos Empleados Credencial";
        
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

        $hojita->setCellValue('A1', "Cédula");
        $hojita->getStyle('A1')->getFont()->setSize(12);
   
        $hojita->setCellValue('B1', "Apellidos");
        $hojita->getStyle('B1')->getFont()->setSize(12);
       
        $hojita->setCellValue('C1', "Nombres");
        $hojita->getStyle('C1')->getFont()->setSize(12);
        
        $hojita->setCellValue('D1', "Tipo Sangre");
        $hojita->getStyle('D1')->getFont()->setSize(12);
        
        $hojita->setCellValue('E1', "Departamento");
        $hojita->getStyle('E1')->getFont()->setSize(12);
    
        $hojita->setCellValue('F1', "Fecha Vencimiento");
        $hojita->getStyle('F1')->getFont()->setSize(12);
        
        $hojita->setCellValue('G1', "Código Comida");
        $hojita->getStyle('G1')->getFont()->setSize(12);

        $hojita->setCellValue('H1', "Estado Credencial");
        $hojita->getStyle('H1')->getFont()->setSize(12);

        $hojita->setCellValue('I1', "Foto");
        $hojita->getStyle('I1')->getFont()->setSize(12);
        
        $hojita->getStyle('A1:I1')->getFont()->setBold(true);
        $hojita->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setAutoFilter("A1:I1");
        
        $i = 1;
        
        foreach ($datos  as $data)
        {
            $i++;
            $hojita->setCellValue('A'.$i,  $data['id_sys_rrhh_cedula']);
            $hojita->setCellValue('B'.$i,  $data['apellidos']);
            $hojita->setCellValue('C'.$i,  $data['nombre']);
            $hojita->setCellValue('D'.$i,  $data['tipo_sangre']);
            $hojita->setCellValue('E'.$i,  $data['departamento']);
            $hojita->setCellValue('F'.$i,  $data['vencimiento_credencial']);
            $hojita->setCellValue('G'.$i,  $data['codigo_temp']);
            $hojita->setCellValue('H'.$i,  $data['estado_credencial'] != null ? $data['estado_credencial'] : 0);
            $hojita->setCellValue('I'.$i,  $data['id_sys_rrhh_cedula']);
        }

        $hojita->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
            
        foreach(range('A','I') as $columnID) {
            $hojita->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
        
        $nombreArchivo='Datos_Empleados_Credencial.xlsx';
        
        $writer = new Xlsx($objPHPExcel);
        $writer->save($nombreArchivo);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
        exit;


endif;

?> 





