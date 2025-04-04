<?php
/* @var $this yii\web\View */
use app\models\SysRrhhEmpleadosNovedades;

?>
<h1>/cash-manager/index</h1>
<?php 
$url = "https://181.198.23.105/easycashdirecto/easycashdirecto.asmx";
// options for ssl in php 5.6.5
$opts = array(
'ssl' => array(
'ciphers' => 'RC4-SHA',
'verify_peer' => false,
'verify_peer_name' => false
)
);

// SOAP 1.2 client
$params = array(
'encoding' => 'UTF-8',
'verifypeer' => false,
'verifyhost' => false,
'soap_version' => SOAP_1_2,
'trace' => 1,
'exceptions' => 1,
'connection_timeout' => 180,
'stream_context' => stream_context_create($opts)
);

    $wsdlUrl = $url . '?WSDL';
    $oSoapClient = new SoapClient($wsdlUrl, $params);


    $datos = (new \yii\db\Query())->select(
    [
        "emp.id_sys_rrhh_cedula",
        "emp.nombres",
        "emp.cta_banco",
        "(select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov as mov
                              inner join sys_rrhh_conceptos as conceptos on (conceptos.id_sys_empresa=mov.id_sys_empresa and conceptos.id_sys_rrhh_concepto= mov.id_sys_rrhh_concepto)
                               where mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula and  mov.anio= rol_mov.anio and mov.mes= rol_mov.mes  and mov.periodo= rol_mov.periodo  and mov.id_sys_empresa= rol_mov.id_sys_empresa and tipo = 'I')
                            - (select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov as mov
                               inner join sys_rrhh_conceptos as conceptos on (conceptos.id_sys_empresa=mov.id_sys_empresa and conceptos.id_sys_rrhh_concepto= mov.id_sys_rrhh_concepto)
                               where mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula and  mov.anio= rol_mov.anio and mov.mes= rol_mov.mes  and mov.periodo= rol_mov.periodo  and mov.id_sys_empresa= rol_mov.id_sys_empresa and tipo = 'E') Total",
        
    ])
    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
    ->where("rol_mov.anio = '2019'")
    ->andwhere("rol_mov.mes=  '9'")
    ->andwhere("rol_mov.periodo=  '2'")
    ->andwhere("rol_mov.id_sys_empresa= '001'")
    ->andwhere("id_sys_rrhh_concepto = 'SUELDO'")
    ->andwhere("emp.id_sys_empresa  = '001'")
    ->andwhere("emp.cta_banco <> 0")
    ->andwhere("emp.id_sys_rrhh_forma_pago = 'A'")
    ->andwhere("id_sys_rrhh_banco = 2")
    ->orderBy("nombres")
    ->all(SysRrhhEmpleadosNovedades::getDb());



if($oSoapClient):
  

      //datos de la empresa
        $empresa      =  $oSoapClient->ConsultaCuenta(['RUC'=> '1391744064001', 'Cod_Servicio'=> 'RP', 'Id_servicio'=> '64']);
        $xml          =  simplexml_load_string($empresa->ConsultaCuentaResult);
   
         //$empresa      =  $xml->DATOSEMPRESA->Nombre;
         //$codempresa   =  $xml->DATOSEMPRESA->Id_Empresa;
         
         echo json_encode($xml);
       
      // echo $codempresa;
      
     //fin de datos de la empresa
     
        //$xmlinput  = '<Envelope><Empresa>PESPESCA S.A</Empresa><Id_Empresa>10662</Id_Empresa><Servicio>RP</Servicio><Id_Servicio>64</Id_Servicio><TipoCuenta>CTE</TipoCuenta><NumeroCuenta>01053648008</NumeroCuenta><Referencia>REFERENCIA</Referencia><FechaInicio>28/10/2019</FechaInicio><FechaVencimiento>30/10/2019</FechaVencimiento><Cabecera></Cabecera> <Item>PA	01053648008	1		1309746376	USD	0000000023644	CTA	0036	AHO	12721008221	C	1309746376	ACOSTA CHAVEZ JONATHAN RAFAEL					ROL DE PAGOS MENSUAL	2019-10-28</Item><Item>PA	01053648008	2		1313687616	USD	0000000036614	CTA	0036	AHO	12303103979	C	1313687616	ACOSTA LOPEZ VICTOR ALFONSO					ROL DE PAGOS MENSUAL	2019-10-28</Item><NumRecs>2</NumRecs></Envelope>';
      
      
       //$respuesta =  $oSoapClient->CargaDirectaXml(['strXmlInput'=> $xmlinput, 'strUsuario'=> 'ADMIN', 'strComputador'=> 'PC-01', 'Empresa'=> 'PESPESCA']);
      
     //  $xml       =  simplexml_load_string($respuesta->CargaDirectaXmlResult);
             
     //  echo $xml->Cabecera->Estado.'<br>';
     //  echo $xml->Cabecera->MensajeRespuesta;
      
    /* $xml_items= '';
     $tipo = 'MENSUAL';
     $cont = 0;
     foreach ($datos as $data):
     
        $array = explode('.',  $data['Total']);
        $valor =  $array[0].''.$array[1]; 
        $cont++;
        
        $xml_items .= "<item>PA\t01053648008\t".$cont."\t\t".trim($data['id_sys_rrhh_cedula'])."\tUSD\t".str_pad($valor, 13, "0", STR_PAD_LEFT)."\tCTA\t0036\tAHO\t".$data['cta_banco']."\tC\t".trim($data['id_sys_rrhh_cedula'])."\t".trim(utf8_encode($data['nombres']))."\t\t\t\t\tROL DE PAGOS ".$tipo."\t".date('Y-m-d')."</item>";
      
     
     endforeach;
     
     $xml_items .= '<NumRecs>'.$cont.'</NumRecs>';
            
     
     $empresa = $oSoapClient->DatosEmpresa(['RUC'=> '1391744064001']);
    
     
     echo json_encode($empresa->DatosEmpresaResult->Empresas);
   
    /* $xml = '<Envelope><Empresa>PESPESCA</Empresa><Id_Empresa>10662</Id_Empresa><Servicio>RP</Servicio><Id_Servicio>0036</Id_Servicio><TipoCuenta>AHO</TipoCuenta><NumeroCuenta>01053648008</NumeroCuenta><Referencia>REFERENCIA</Referencia><FechaInicio>06/07/2009</FechaInicio><FechaVencimiento>01/06/2010</FechaVencimiento><Cabecera></Cabecera>'.$xml_items.'</Envelope>';
 
     $result  = $oSoapClient->CargaDirectaXml(['strXmlInput'=> $xml, 'strUsuario'=> 'ADMINISTRADOR', 'strComputador'=> 'PC-SISTEMAS','Empresa'=> 'PESPESCA']);
 
     
     if($result):
        echo json_encode($result->CargaDirectaXmlResult);
     endif;
     */
   
else:

  echo "No existe"; 

endif;

?>