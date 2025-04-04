<?php
/* @var $this yii\web\View */

use app\models\SysAdmAreas;
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


$meses = [1 => 'ENERO',  2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE' ];

$nomArea = SysAdmAreas::find()->where(['id_sys_adm_area' => $area])->one();


$tipo = '';

if($periodo == 1 ):

$tipo = 'Quincenal';

elseif($periodo == 2 ) :

$tipo = 'ROL';

elseif($periodo ==  90):

$tipo = 'BENE';

elseif($periodo == 71):

$tipo = 'Dec. Tercero';

elseif($periodo == 72):

$tipo = 'Dec. Cuarto';

endif;

$dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));

$dia = $dia > 30 ? '30': $dia;


if($nomArea->area == 'UNIDAD DE CALIDAD HIGIENE Y PRODUCTO FINAL'){
    $nomArea->area = 'UNI. CALIDAD';
}


 if($datos):
        
        $objPHPExcel =  new Spreadsheet();
 
        $titulo= $tipo." ".$meses[$mes]." ".$nomArea->area;
        
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
        $imagenLogo->setCoordinates('E2');
        $imagenLogo->setWidthAndHeight(260,200);
        $imagenLogo->setWorksheet($hojita);
        
        if($periodo == 2):
            $hojita->setCellValue('C5', "ROL DE ".$meses[$mes]." ".$anio);
            $hojita->getStyle('C5')->getFont()->setSize(15);
            $hojita->getStyle('C5')->getFont()->setBold(true);
            $hojita->mergeCells('C5:G5');
            $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        else:
            $hojita->setCellValue('C5', "ROL BENEFICIOS SOCIALES DE ".$meses[$mes]." ".$anio);
            $hojita->getStyle('C5')->getFont()->setSize(15);
            $hojita->getStyle('C5')->getFont()->setBold(true);
            $hojita->mergeCells('C5:G5');
            $hojita->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        endif;

        $hojita->setCellValue('C6', "DEPARTAMENTO DE ".$nomArea->area);
        $hojita->getStyle('C6')->getFont()->setSize(15);
        $hojita->getStyle('C6')->getFont()->setBold(true);
        $hojita->mergeCells('C6:G6');
        $hojita->getStyle('C6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        
        $hojita->setCellValue('B9', "Cuenta de Mayor/Codigo");
        $hojita->getStyle('B9')->getFont()->setSize(12);
        $hojita->getStyle('B9')->getFont()->setBold(true);
        $hojita->getStyle('B9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('C9', "Cuenta Mayor Nombre");
        $hojita->getStyle('C9')->getFont()->setSize(12);
        $hojita->getStyle('C9')->getFont()->setBold(true);
        $hojita->getStyle('C9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('D9', "Debito");
        $hojita->getStyle('D9')->getFont()->setSize(12);
        $hojita->getStyle('D9')->getFont()->setBold(true);
        $hojita->getStyle('D9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('E9', "Credito");
        $hojita->getStyle('E9')->getFont()->setSize(12);
        $hojita->getStyle('E9')->getFont()->setBold(true);
        $hojita->getStyle('E9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('F9', "Comentarios");
        $hojita->getStyle('F9')->getFont()->setSize(12);
        $hojita->getStyle('F9')->getFont()->setBold(true);
        $hojita->getStyle('F9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $hojita->setCellValue('G9', "Centro Costo");
        $hojita->getStyle('G9')->getFont()->setSize(12);
        $hojita->getStyle('G9')->getFont()->setBold(true);
        $hojita->getStyle('G9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setCellValue('H9', "Area");
        $hojita->getStyle('H9')->getFont()->setSize(12);
        $hojita->getStyle('H9')->getFont()->setBold(true);
        $hojita->getStyle('H9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $hojita->setAutoFilter("B9:H9");

        $i = 9;

        if ($datos):

            if($periodo == 2):

                $totaldebito = 0;

                foreach($datos as $dat): 

                    $totalSobreTiempo = 0;
                    $cuentaMayor = '';
                    $cuentaMayorNombre = '';
                    
                    $listhaberes      =  ListHaberes($anio, $mes, $periodo, $dat['id_sys_adm_ccosto']); 

                    foreach($listhaberes as $haberes):

                        $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $area,$dat['id_sys_adm_ccosto']);

                        if($dataConcepto !== false):

                            if($dataConcepto['cta_mayor_nombre'] == 'Sobretiempo'):

                                $totalSobreTiempo += $dataConcepto['valor'];
                                $cuentaMayor = $dataConcepto['cta_debito'];
                                $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];

                            else:

                                $totaldebito += $dataConcepto['valor'];

                                if($haberes['id_sys_rrhh_concepto'] != 'FOND- RESERV ANT'):
                                    $i++;

                                    $hojita->setCellValue('B'.$i,  $dataConcepto['cta_debito']);
                                    $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                                    $hojita->setCellValue('D'.$i,  $dataConcepto['valor']);
                                    $hojita->setCellValue('E'.$i,  "");
                                    $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                                    $hojita->setCellValue('G'.$i,  substr($dat['id_sys_adm_ccosto'],0,-2));
                                    $hojita->setCellValue('H'.$i,  $dat['id_sys_adm_ccosto']);
                                else:

                                    $i++;

                                    $hojita->setCellValue('B'.$i,  $dataConcepto['cta_debito']);
                                    $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                                    $hojita->setCellValue('D'.$i,  $dataConcepto['valor']);
                                    $hojita->setCellValue('E'.$i,  "");
                                    $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                                    $hojita->setCellValue('G'.$i,  "");
                                    $hojita->setCellValue('H'.$i,  "");

                                endif;

                            endif;

                        endif;

                    endforeach;

                    if($totalSobreTiempo != 0):

                        $i++;

                        $hojita->setCellValue('B'.$i,  $cuentaMayor);
                        $hojita->setCellValue('C'.$i,  $cuentaMayorNombre);
                        $hojita->setCellValue('D'.$i,  $totalSobreTiempo);
                        $hojita->setCellValue('E'.$i,  "");
                        $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                        $hojita->setCellValue('G'.$i,  substr($dat['id_sys_adm_ccosto'],0,-2));
                        $hojita->setCellValue('H'.$i,  $dat['id_sys_adm_ccosto']);
                    
                    endif;

                    $totaldebito = $totaldebito + $totalSobreTiempo;

                endforeach;

                $totalcredito = 0;
                $centroCostos = [];

                foreach($datos as $dat): 

                    array_push($centroCostos,$dat['id_sys_adm_ccosto']);

                endforeach;

                $listdescuentos      =  ListDescuentos($anio, $mes, $periodo, $area); 

                foreach($listdescuentos as $descuento):

                    if($descuento['id_sys_rrhh_concepto'] == 'DES_HORAS_NL' || $descuento['id_sys_rrhh_concepto'] == 'DESC_DIAS_NL'):

                        foreach($datos as $dat):
                            
                            $dataConcepto = ObtenerConceptoCredito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$dat['id_sys_adm_ccosto']);
                            
                            if($dataConcepto !== false):

                                $totalcredito += $dataConcepto['valor'];

                                if($dataConcepto['valor'] != 0):

                                    $i++;

                                    $hojita->setCellValue('B'.$i,  $dataConcepto['cta_credito']);
                                    $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                                    $hojita->setCellValue('D'.$i,  "");
                                    $hojita->setCellValue('E'.$i,  $dataConcepto['valor']);
                                    $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                                    $hojita->setCellValue('G'.$i,  substr($dat['id_sys_adm_ccosto'],0,-2));
                                    $hojita->setCellValue('H'.$i,  $dat['id_sys_adm_ccosto']);
                                endif;

                            endif;

                        endforeach;

                    else:

                        $dataConcepto = ObtenerConceptoCredito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$centroCostos);

                        $totalcredito += $dataConcepto['valor'];

                        if($dataConcepto['valor'] != 0):

                            $i++;

                            $hojita->setCellValue('B'.$i,  $dataConcepto['cta_credito']);
                            $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                            $hojita->setCellValue('D'.$i,  "");
                            $hojita->setCellValue('E'.$i,  $dataConcepto['valor']);
                            $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                            $hojita->setCellValue('G'.$i,  "");
                            $hojita->setCellValue('H'.$i,  "");

                        endif;

                    endif;

                endforeach;

                $totalsalario = $totaldebito - $totalcredito;

                $i++;

                $hojita->setCellValue('B'.$i,  "2105-001-00");
                $hojita->setCellValue('C'.$i,  "Salarios");
                $hojita->setCellValue('D'.$i,  "");
                $hojita->setCellValue('E'.$i,  $totalsalario);
                $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                $hojita->setCellValue('G'.$i,  "");
                $hojita->setCellValue('H'.$i,  "");

                $totalcredito = $totalcredito + $totalsalario;

                $i++;

                $hojita->setCellValue('B'.$i,  "");
                $hojita->setCellValue('C'.$i,  "");
                $hojita->setCellValue('D'.$i,  $totaldebito);
                $hojita->setCellValue('E'.$i,  $totalcredito);
                $hojita->setCellValue('F'.$i,  "");
                $hojita->setCellValue('G'.$i,  "");
                $hojita->setCellValue('H'.$i,  "");

            else:
                
                $totaldebito = 0;

                foreach($datos as $dat): 
                            
                    $listhaberes      =  ListProvisiones($anio, $mes, $periodo, $dat['id_sys_adm_ccosto']); 
                    $totalIess = 0;
                    $cuentaMayor = '';
                    $cuentaMayorNombre = '';

                    foreach($listhaberes as $haberes):

                        $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $haberes['id_sys_rrhh_concepto'], $area,$dat['id_sys_adm_ccosto']);

                        if($dataConcepto['cta_mayor_nombre'] == 'Aporte al Iess'):

                            $totalIess += $dataConcepto['valor'];
                            $cuentaMayor = $dataConcepto['cta_debito'];
                            $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];
                                    
                        else:

                            $totaldebito += $dataConcepto['valor'];

                            if($dataConcepto['cta_mayor_nombre'] == '14to. Sueldo/Decimo Cuarto Sueldo' || $dataConcepto['cta_mayor_nombre'] == '13er. Sueldo/Decimo Tercer Sueldo'):

                                $concepto = explode("/", $dataConcepto['cta_mayor_nombre']);

                                $dataConcepto['cta_mayor_nombre'] = $concepto[0];

                            endif;

                            $i++;

                            $hojita->setCellValue('B'.$i,  $dataConcepto['cta_debito']);
                            $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                            $hojita->setCellValue('D'.$i,  $dataConcepto['valor']);
                            $hojita->setCellValue('E'.$i,  "");
                            $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                            $hojita->setCellValue('G'.$i,  substr($dat['id_sys_adm_ccosto'],0,-2));
                            $hojita->setCellValue('H'.$i,  $dat['id_sys_adm_ccosto']);

                        endif;

                    endforeach;

                    $i++;

                    $hojita->setCellValue('B'.$i,  $cuentaMayor);
                    $hojita->setCellValue('C'.$i,  $cuentaMayorNombre);
                    $hojita->setCellValue('D'.$i,  $totalIess);
                    $hojita->setCellValue('E'.$i,  "");
                    $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                    $hojita->setCellValue('G'.$i,  substr($dat['id_sys_adm_ccosto'],0,-2));
                    $hojita->setCellValue('H'.$i,  $dat['id_sys_adm_ccosto']);

                    $totaldebito = $totaldebito + $totalIess;

                endforeach;

                $centroCostos = [];
                $totalcredito = 0;

                foreach($datos as $dat): 

                    array_push($centroCostos,$dat['id_sys_adm_ccosto']);

                endforeach;

                $listdescuentos      =  ListProvisionesXArea($anio, $mes, $periodo, $area); 
                $totalIessCredito = 0;
                $cuentaMayor = "";
                $cuentaMayorNombre = "";

                foreach($listdescuentos as $descuento):

                    $dataConcepto = ObtenerConceptoDebito($anio, $mes, $periodo, $descuento['id_sys_rrhh_concepto'], $area,$centroCostos);

                    if($dataConcepto['cta_mayor_nombre'] == 'Aporte al Iess'):

                        $totalIessCredito += $dataConcepto['valor'];
                        $cuentaMayor = $dataConcepto['cta_credito'];
                        $cuentaMayorNombre = $dataConcepto['cta_mayor_nombre'];
                            
                    else:

                        $totalcredito += $dataConcepto['valor'];

                        if($dataConcepto['cta_mayor_nombre'] == '14to. Sueldo/Decimo Cuarto Sueldo' || $dataConcepto['cta_mayor_nombre'] == '13er. Sueldo/Decimo Tercer Sueldo'):

                            $concepto = explode("/", $dataConcepto['cta_mayor_nombre']);

                            $dataConcepto['cta_mayor_nombre'] = $concepto[1];

                        endif;

                        $i++;

                        $hojita->setCellValue('B'.$i,  $dataConcepto['cta_credito']);
                        $hojita->setCellValue('C'.$i,  $dataConcepto['cta_mayor_nombre']);
                        $hojita->setCellValue('D'.$i,  "");
                        $hojita->setCellValue('E'.$i,  $dataConcepto['valor']);
                        $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                        $hojita->setCellValue('G'.$i,  "");
                        $hojita->setCellValue('H'.$i,  "");

                    endif;

                endforeach;

                $i++;

                $hojita->setCellValue('B'.$i,  $cuentaMayor);
                $hojita->setCellValue('C'.$i,  $cuentaMayorNombre);
                $hojita->setCellValue('D'.$i,  "");
                $hojita->setCellValue('E'.$i,  $totalIessCredito);
                $hojita->setCellValue('F'.$i,  "Reg. Contab Roles ".ucfirst(strtolower($datos[0]['area'])).' '.$meses[$mes].' '.$anio);
                $hojita->setCellValue('G'.$i,  "");
                $hojita->setCellValue('H'.$i,  "");

                $totalcredito = $totalcredito + $totalIessCredito;

                $i++;
                $hojita->setCellValue('B'.$i,  "");
                $hojita->setCellValue('C'.$i,  "");
                $hojita->setCellValue('D'.$i,  $totaldebito);
                $hojita->setCellValue('E'.$i,  $totalcredito);
                $hojita->setCellValue('F'.$i,  "");
                $hojita->setCellValue('G'.$i,  "");
                $hojita->setCellValue('H'.$i,  "");

            endif;

        endif;
        
        
        foreach(range('B','H') as $columnID) {
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


function ListHaberes($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("conceptos.tipo = 'I'")
    ->andwhere("rol_mov.valor > 0")
    ->andWhere("co.id_sys_adm_ccosto = '{$area}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());

    return $datos;
}

function ListProvisiones($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
    ->where("rol_mov.anio = '{$anio}'")
    ->andwhere("rol_mov.mes=  '{$mes}'")
    ->andwhere("rol_mov.periodo=  '{$periodo}'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere(["conceptos.tipo"=>['I','E']])
    ->andwhere("rol_mov.valor > 0")
    ->andWhere("co.id_sys_adm_ccosto = '{$area}'")
    ->andwhere("conceptos.id_sys_empresa  = '001'")
    ->distinct()
    ->orderby("conceptos.orden")
    ->all(SysRrhhEmpleadosNovedades::getDb());

    return $datos;
}

function ListProvisionesXArea($anio, $mes, $periodo, $area){
    $datos = [];

    if($area == 1):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 4):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
         $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere(["conceptos.tipo"=>['I','E']])
            ->andwhere("rol_mov.valor > 0")
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andwhere("rol_mov.valor > 0")
        ->andWhere(["area.id_sys_adm_area"=>[3]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere(["conceptos.tipo"=>['I','E']])
            ->andwhere("rol_mov.valor > 0")
            ->andWhere(["area.id_sys_adm_area"=>[2]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_ccostos as co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere(["conceptos.tipo"=>['I','E']])
        ->andWhere(["area.id_sys_adm_area"=>[5]])
        ->andwhere("rol_mov.valor > 0")
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    endif;

    return $datos;
}

function ListDescuentos($anio, $mes, $periodo, $area){
    
    $datos = [];
    
    if($area == 1):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 4):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
                ->andWhere(["area.id_sys_adm_area"=>[2]])
                ->andwhere("conceptos.id_sys_empresa  = '001'")
                ->distinct()
                ->orderby("conceptos.orden")
                ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
        ->andWhere(["area.id_sys_adm_area"=>[3]])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->distinct()
        ->orderby("conceptos.orden")
        ->all(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
            $datos =  (new \yii\db\Query())->select(['rol_mov.id_sys_rrhh_concepto', 'conceptos.orden'])
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
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->distinct()
            ->orderby("conceptos.orden")
            ->all(SysRrhhEmpleadosNovedades::getDb());
    endif;
    
    
    return $datos;
    
}
function ObtenerConceptoDebito($anio, $mes, $periodo, $concepto, $area,$ccosto){
    
    if($area == 1):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[1,8]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[3]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_credito","conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area ==4):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[4]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 2):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[2]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());    
    elseif($area == 6):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[6]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());   
    elseif($area == 5):
        return   (new \yii\db\Query())->select(['conceptos.cta_debito','conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->groupBy(["conceptos.cta_debito", "conceptos.cta_mayor_nombre","conceptos.cta_credito"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    endif;
    
   
}

function ObtenerConceptoCredito($anio, $mes, $periodo, $concepto, $area,$ccosto){
    
    if($area == 1):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[1,8]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 3):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[3]])
            ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area ==4):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[4]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 6):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[6]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());  
    elseif($area == 2):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
        ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
        ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->where("rol_mov.anio = '{$anio}'")
        ->andwhere("rol_mov.mes=  '{$mes}'")
        ->andwhere("rol_mov.periodo=  '{$periodo}'")
        ->andwhere("rol_mov.id_sys_empresa= '001'")
        ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
        ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
        ->andwhere("conceptos.id_sys_empresa  = '001'")
        ->andWhere(["area.id_sys_adm_area"=>[2]])
        ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
        ->one(SysRrhhEmpleadosNovedades::getDb());
    elseif($area == 5):
        return   (new \yii\db\Query())->select(['conceptos.cta_credito','conceptos.cta_mayor_nombre','sum(rol_mov.valor) as valor'])
            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
            ->join('INNER JOIN', 'sys_rrhh_conceptos as conceptos','rol_mov.id_sys_rrhh_concepto = conceptos.id_sys_rrhh_concepto')
            ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("rol_mov.anio = '{$anio}'")
            ->andwhere("rol_mov.mes=  '{$mes}'")
            ->andwhere("rol_mov.periodo=  '{$periodo}'")
            ->andwhere("rol_mov.id_sys_empresa= '001'")
            ->andwhere("conceptos.id_sys_rrhh_concepto = '{$concepto}'")
            ->andwhere(["emp.id_sys_adm_ccosto"=>$ccosto])
            ->andwhere("conceptos.id_sys_empresa  = '001'")
            ->andWhere(["area.id_sys_adm_area"=>[5]])
            ->groupBy(["conceptos.cta_credito", "conceptos.cta_mayor_nombre"])
            ->one(SysRrhhEmpleadosNovedades::getDb());
    endif;
}
function ObtenerAreaCcosto($id_sys_adm_ccosto){
       
    return   (new \yii\db\Query())->select(['centro_costo'])
    ->from("sys_adm_ccostos")
    ->where("id_sys_adm_ccosto = '{$id_sys_adm_ccosto}'")
    ->andwhere("id_sys_empresa =  '001'")
    ->scalar(SysAdmCcostos::getDb());
    
}



?>
