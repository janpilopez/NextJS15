<?php
use app\models\SysRrhhEmpleadosPermisosIngresos;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysEmpresa;
use app\models\SysRrhhPermisos;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosAtencionDet;
use app\models\SysRrhhEmpleadosContratos;

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 =>'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

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

    $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

    $objPHPExcel =  new Spreadsheet();
    
    $titulo= "Informe Ingreso Por Persona";
    
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
    
    $hojita->setCellValue('D2', "Ingresos a la Planta de: ".$empleado->nombres);
    $hojita->getStyle('D2')->getFont()->setSize(15);
    $hojita->getStyle('D2')->getFont()->setBold(true);
    $hojita->mergeCells('D2:F2');
    $hojita->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $hojita->setCellValue('D3', "Desde ".$fechaini." hasta ".$fechafin);
    $hojita->getStyle('D3')->getFont()->setSize(15);
    $hojita->getStyle('D3')->getFont()->setBold(true);
    $hojita->mergeCells('D3:F3');
    $hojita->getStyle('D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $hojita->setCellValue('A5', "Fecha Ingreso");
    $hojita->getStyle('A5')->getFont()->setSize(12);
    $hojita->getStyle('A5')->getFont()->setBold(true);
    $hojita->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $hojita->setCellValue('B5', "Hora Ingreso");
    $hojita->getStyle('B5')->getFont()->setSize(12);
    $hojita->getStyle('B5')->getFont()->setBold(true);
    $hojita->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $hojita->setCellValue('C5', "Fecha Salida");
    $hojita->getStyle('C5')->getFont()->setSize(12);
    $hojita->getStyle('C5')->getFont()->setBold(true);
    $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $hojita->setCellValue('D5', "Hora Salida");
    $hojita->getStyle('D5')->getFont()->setSize(12);
    $hojita->getStyle('D5')->getFont()->setBold(true);
    $hojita->getStyle('D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $hojita->setCellValue('E5', "Foto Documento");
    $hojita->getStyle('E5')->getFont()->setSize(12);
    $hojita->getStyle('E5')->getFont()->setBold(true);
    $hojita->getStyle('E5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $hojita->setCellValue('F5', "Foto Firma");
    $hojita->getStyle('F5')->getFont()->setSize(12);
    $hojita->getStyle('F5')->getFont()->setBold(true);
    $hojita->getStyle('F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    $hojita->setAutoFilter("A5:F5");
    
    
    $i = 5;
    
    if($datos):
    
        foreach ($datos as $index => $item): 

            $db =  $_SESSION['db'];

            $fotoFirma =   Yii::$app->$db->createCommand("select foto_firma, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_firma as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$item['id_sys_rrhh_empleados_permisos_ingresos']}")->queryOne();
            $fotoDocumento =   Yii::$app->$db->createCommand("select foto_documento, baze64 from sys_rrhh_empleados_permisos_ingresos_det cross apply (select foto_documento as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$item['id_sys_rrhh_cedula']}' and estado = 1 and id_sys_rrhh_empleados_permisos_ingresos= {$item['id_sys_rrhh_empleados_permisos_ingresos']}")->queryOne();
            $solicitud = SysRrhhEmpleadosPermisosIngresos::find()->where(['id' => $item['id_sys_rrhh_empleados_permisos_ingresos']])->one();

            $newfecha =  date('j-n-Y', strtotime($solicitud['fecha_ingreso']));

            $i++;
            $hojita->getStyle('A'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hojita->getStyle('A'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $hojita->setCellValue('A'.$i, $item['fecha_ingreso']);
            $hojita->getStyle('B'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hojita->getStyle('B'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $hojita->setCellValue('B'.$i, date('H:i:s', strtotime($item['hora_ingreso'])));
            $hojita->getStyle('C'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hojita->getStyle('C'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $hojita->setCellValue('C'.$i, $item['fecha_salida']);
            $hojita->getStyle('D'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hojita->getStyle('D'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $hojita->setCellValue('D'.$i, date('H:i:s', strtotime($item['hora_salida'])));
            
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(50);
            
            $imagendocumento = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            if(file_exists('C:\fotos\documentos\p'.$item['id_sys_rrhh_cedula'].'_'.$newfecha.'.png')){
                $imagendocumento->setPath('C:\fotos\documentos\p'.$item['id_sys_rrhh_cedula'].'_'.$newfecha.'.png');
                $imagendocumento->setCoordinates('E'.$i);
                $imagendocumento->setOffsetX(60);                         
                $imagendocumento->setOffsetY(10); 
                $imagendocumento->setWidth(20);
                $imagendocumento->setHeight(40);  
                $imagendocumento->setWorksheet($hojita);    
            }else{
                $hojita->getStyle('E'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $hojita->getStyle('E'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $hojita->setCellValue('E'.$i, '');
            }

            $imagenfirma = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            if(file_exists('C:\fotos\firmas\p'.$item['id_sys_rrhh_cedula'].'_'.$newfecha.'.png')){
                $imagenfirma->setPath('C:\fotos\firmas\p'.$item['id_sys_rrhh_cedula'].'_'.$newfecha.'.png');
                $imagenfirma->setCoordinates('F'.$i);
                $imagenfirma->setOffsetX(40);                         
                $imagenfirma->setOffsetY(10); 
                $imagenfirma->setWidth(20);
                $imagenfirma->setHeight(40);  
                $imagenfirma->setWorksheet($hojita); 
            }else{
                $hojita->getStyle('F'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $hojita->getStyle('F'.$i)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $hojita->setCellValue('F'.$i, '');
            } 

                
        endforeach;

        foreach(range('A','F') as $columnID) {
            $hojita->getColumnDimension($columnID)->setWidth(30);    
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

?>

