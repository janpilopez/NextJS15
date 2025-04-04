<?php
use yii\helpers\Html;
use app\assets\AppAsset;
use app\models\SysAdmCargos;
use app\models\SysAdmPeriodoVacaciones;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysRrhhEmpleadosSueldos;

AppAsset::register($this);
$this->title = 'Certificado Laboral';

$empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();

$contrato =  SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->andWhere(['activo'=> '1'])->one();

$contratoSalida = (new \yii\db\Query())
    ->select('*')
    ->from('sys_rrhh_empleados_contratos')
    ->where("id_sys_rrhh_cedula = '$model->id_sys_rrhh_cedula'")
    ->andWhere("fecha_salida = (SELECT MAX(fecha_salida) from sys_rrhh_empleados_contratos 
    where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula')")
    ->all(SysRrhhEmpleadosContratos::getDb());

$cargo = SysAdmCargos::find()
->innerJoin('sys_rrhh_empleados','sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo')
->andwhere(['sys_rrhh_empleados.id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();

$sueldo = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['estado'=> 'A'])->one();

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

$dias = [ 0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles' , 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

?>
<div class= "row">
     <div class = "col-md-12">
      <table>
          <tr>
            <td style="text-align:right;"><img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="70" width="280" class ="text-center"></td>
          </tr>
      </table>
     </div>
</div>
<div class = "row margen-left">
   <div class = "col-md-12 margen-left">
     <br>
     <br>
     <p>Montecristi, <?= date("d", strtotime($model->fecha_creacion))?> de <?= $meses[date("n", strtotime($model->fecha_creacion))]?> del <?= date("Y", strtotime($model->fecha_creacion))?></p>
     <br>
     <p style ="text-align: center;" class="title">CERTIFICACIÓN LABORAL</span></p>
     <br>
     <br>
     <p>A quien corresponda:</p>
     <p></p>
     <br>
     <br>
     <?php
     if($contrato){
     ?> 
     <p style ="text-align: justify;">Tengo a bien certificar que el/la Sr./Sra./Srta. <span class='negrita'><?= strtoupper($empleado->nombres)?></span>, con cédula de ciudadanía número <?= $empleado->id_sys_rrhh_cedula?>, labora en la empresa <?= $empresa->razon_social?> desde el <?= date("d", strtotime($contrato->fecha_ingreso))?> de <?= $meses[date("n", strtotime($contrato->fecha_ingreso))]?> del <?= date("Y", strtotime($contrato->fecha_ingreso))?> hasta la actualidad,
      desempeñando el cargo de <span class='negrita'><?= ($cargo->cargo)?></span>, percibiendo una remuneración mensual de $<?= $numero_con_decimales=floatval($sueldo->sueldo); $son=number_format($numero_con_decimales,2,".",""); $V=new EnLetras();?> ( <?php echo $V->ValorEnLetras($son,"d&oacute;lares");?>).</p>
     <?php }else{
     ?>
     <p style ="text-align: justify;">Tengo a bien certificar que el/la Sr./Sra./Srta. <span class='negrita'><?= strtoupper($empleado->nombres)?></span>, con cédula de ciudadanía número <?= $empleado->id_sys_rrhh_cedula?>, laboró en la empresa <?= $empresa->razon_social?> como <?= ($cargo->cargo)?>, desde el <?= date("d", strtotime($contratoSalida[0]['fecha_ingreso']))?> de <?= $meses[date("n", strtotime($contratoSalida[0]['fecha_ingreso']))]?> del <?= date("Y", strtotime($contratoSalida[0]['fecha_ingreso']))?> 
     hasta el <?= date("d", strtotime($contratoSalida[0]['fecha_salida']))?> de <?= $meses[date("n", strtotime($contratoSalida[0]['fecha_salida']))]?> del <?= date("Y", strtotime($contratoSalida[0]['fecha_salida']))?>, demostrando durante su permanencia responsabilidad, honestidad y dedicación en las labores que le fueron encomendadas.</p> 
     <?php }?> 
     <br>
     <p>Se emite la presente certificación a solicitud del interesado, para los fines que crea conveniente.</p>
     <br>
     <br>
     <p>Atentamente,</p>
     <br>
     <br>
     <br>
     <br>
   </div>
    <table>
       <tr>
         <td width = "50%"><center><img src=" <?= Yii::getAlias('@webroot')."/".trim("img/firmacertificado.png")?>" height="100" width="200" class ="text-center"></center></td>
       <tr>
       <tr>
         <td width = "50%" class = "text-center" ><p class='negrita'>Ab. Soledad Orbes</p></td>
       <tr>
       <tr>
         <td width = "50%" class = "text-center"><p class='negrita'>Jefe de Talento Humano</p></td>
       <tr>
       <tr>
         <td width = "50%" class = "text-center"><p class='negrita'><?= $empresa->razon_social?></p></td>
       <tr>
    </table>
</div>

<?php
class EnLetras
{
var $Void = "";
var $SP = " ";
var $Dot = ".";
var $Zero = "0";
var $Neg = "Menos";
 
function ValorEnLetras($x, $Moneda )
{
$s="";
$Ent="";
$Frc="";
$Signo="";
 
if(floatVal($x) < 0)
$Signo = $this->Neg . " ";
else
$Signo = "";
 
if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
$s = number_format($x,2,'.','');
else
$s = number_format($x,0,'.','');
 
$Pto = strpos($s, $this->Dot);
 
if ($Pto === false)
{
$Ent = $s;
$Frc = $this->Void;
}
else
{
$Ent = substr($s, 0, $Pto );
$Frc = substr($s, $Pto+1);
}
 
if($Ent == $this->Zero || $Ent == $this->Void)
$s = "Cero ";
elseif( strlen($Ent) > 7)
{
$s = $this->SubValLetra(intval( substr($Ent, 0, strlen($Ent) - 6))) .
"Millones " . $this->SubValLetra(intval(substr($Ent,-6, 6)));
}
else
{
$s = $this->SubValLetra(intval($Ent));
}
 
if (substr($s,-9, 9) == "Millones " || substr($s,-7, 7) == "Millón ")
$s = $s . "de ";
 
$s = $s . $Moneda;
 
if($Frc != $this->Void)
{
$s = $s . " Con " . $this->SubValLetra(intval($Frc)) . "Centavos";
//$s = $s . " " . $Frc . "/100";
}
return ($Signo . $s . " ");
 
}
 
 
function SubValLetra($numero)
{
$Ptr="";
$n=0;
$i=0;
$x ="";
$Rtn ="";
$Tem ="";
 
$x = trim("$numero");
$n = strlen($x);
 
$Tem = $this->Void;
$i = $n;
 
while( $i > 0)
{
$Tem = $this->Parte(intval(substr($x, $n - $i, 1).
str_repeat($this->Zero, $i - 1 )));
If( $Tem != "Cero" )
$Rtn .= $Tem . $this->SP;
$i = $i - 1;
}
 
 
//--------------------- GoSub FiltroMil ------------------------------
$Rtn=str_replace(" Mil Mil", " Mil", $Rtn );
while(1)
{
$Ptr = strpos($Rtn, "Mil ");
If(!($Ptr===false))
{
If(! (strpos($Rtn, "Mil ",$Ptr + 1) === false ))
$this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
Else
break;
}
else break;
}
 
//--------------------- GoSub FiltroCiento ------------------------------
$Ptr = -1;
do{
$Ptr = strpos($Rtn, "Cien ", $Ptr+1);
if(!($Ptr===false))
{
$Tem = substr($Rtn, $Ptr + 5 ,1);
if( $Tem == "M" || $Tem == $this->Void)
;
else
$this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
}
}while(!($Ptr === false));
 
//--------------------- FiltroEspeciales ------------------------------
$Rtn=str_replace("Diez Un", "Once", $Rtn );
$Rtn=str_replace("Diez Dos", "Doce", $Rtn );
$Rtn=str_replace("Diez Tres", "Trece", $Rtn );
$Rtn=str_replace("Diez Cuatro", "Catorce", $Rtn );
$Rtn=str_replace("Diez Cinco", "Quince", $Rtn );
$Rtn=str_replace("Diez Seis", "Dieciseis", $Rtn );
$Rtn=str_replace("Diez Siete", "Diecisiete", $Rtn );
$Rtn=str_replace("Diez Ocho", "Dieciocho", $Rtn );
$Rtn=str_replace("Diez Nueve", "Diecinueve", $Rtn );
$Rtn=str_replace("Veinte Un", "Veintiun", $Rtn );
$Rtn=str_replace("Veinte Dos", "Veintidos", $Rtn );
$Rtn=str_replace("Veinte Tres", "Veintitres", $Rtn );
$Rtn=str_replace("Veinte Cuatro", "Veinticuatro", $Rtn );
$Rtn=str_replace("Veinte Cinco", "Veinticinco", $Rtn );
$Rtn=str_replace("Veinte Seis", "Veintiseís", $Rtn );
$Rtn=str_replace("Veinte Siete", "Veintisiete", $Rtn );
$Rtn=str_replace("Veinte Ocho", "Veintiocho", $Rtn );
$Rtn=str_replace("Veinte Nueve", "Veintinueve", $Rtn );
 
//--------------------- FiltroUn ------------------------------
If(substr($Rtn,0,1) == "M") $Rtn = " " . $Rtn;
//--------------------- Adicionar Y ------------------------------
for($i=65; $i<=88; $i++)
{
If($i != 77)
$Rtn=str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
}
$Rtn=str_replace("*", "a" , $Rtn);
return($Rtn);
}
 
 
function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
{
$x = substr($x, 0, $Ptr) . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
}
 
 
function Parte($x)
{
$Rtn='';
$t='';
$i='';
Do
{
switch($x)
{
Case 0: $t = "Cero";break;
Case 1: $t = "Un";break;
Case 2: $t = "Dos";break;
Case 3: $t = "Tres";break;
Case 4: $t = "Cuatro";break;
Case 5: $t = "Cinco";break;
Case 6: $t = "Seis";break;
Case 7: $t = "Siete";break;
Case 8: $t = "Ocho";break;
Case 9: $t = "Nueve";break;
Case 10: $t = "Diez";break;
Case 20: $t = "Veinte";break;
Case 30: $t = "Treinta";break;
Case 40: $t = "Cuarenta";break;
Case 50: $t = "Cincuenta";break;
Case 60: $t = "Sesenta";break;
Case 70: $t = "Setenta";break;
Case 80: $t = "Ochenta";break;
Case 90: $t = "Noventa";break;
Case 100: $t = "Cien";break;
Case 200: $t = "Doscientos";break;
Case 300: $t = "Trescientos";break;
Case 400: $t = "Cuatrocientos";break;
Case 500: $t = "Quinientos";break;
Case 600: $t = "Seiscientos";break;
Case 700: $t = "Setecientos";break;
Case 800: $t = "Ochocientos";break;
Case 900: $t = "Novecientos";break;
Case 1000: $t = "Mil";break;
Case 2000: $t = "Dos Mil";break;
Case 3000: $t = "Tres Mil";break;
Case 4000: $t = "Cuatro Mil";break;
Case 5000: $t = "Cinco Mil";break;
Case 6000: $t = "Seis Mil";break;
Case 1000000: $t = "Millón";break;
}
 
If($t == $this->Void)
{
$i = $i + 1;
$x = $x / 1000;
If($x== 0) $i = 0;
}
else
break;
 
}while($i != 0);
 
$Rtn = $t;
Switch($i)
{
Case 0: $t = $this->Void;break;
Case 1: $t = " Mil";break;
Case 2: $t = " Millones";break;
Case 3: $t = " Billones";break;
}
return($Rtn . $t);
}
 
}

?>