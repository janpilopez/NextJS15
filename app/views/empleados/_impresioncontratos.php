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
$this->title = 'Impresión Contrato';

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class = "row margen-left">
   <div class = "col-md-12 margen-left">
     <br>
     <p style ="text-align: center;font-size: 10.5px;font-weight: bold;">CONTRATO ESPECIAL EMERGENTE A JORNADA COMPLETA</span></p>
     <br>
     <p style ="text-align: justify;font-size: 10.5px;">Comparecen, ante el señor Inspector del Trabajo, por una parte, PESPESCA S.A., representada por <span style="font-weight: bold;">MEZA CHICA MARIA FERNANDA</span> en su calidad de <span style="font-weight: bold;"><i>EMPLEADOR</i></span>  y  por otra parte el/la señor (a/ita) 
     <span style="font-weight: bold;"><?= $datos['nombres'] ?></span> portador  de  la  cédula  de  ciudadanía  No. <span style="font-weight: bold;"><?= $datos['id_sys_rrhh_cedula'] ?></span>, en  calidad  de <span style="font-weight: bold;"><i>TRABAJADOR</i></span>. Los comparecientes son ecuatorianos, respectivamente, domiciliados 
     la ciudad de Montecristi y capaces para contratar, quienes libre y voluntariamente convienen en celebrar un contrato de trabajo especial emergente a jornada completa con sujeción a las declaraciones y estipulaciones contenidas en las siguientes cláusulas:</p>
     <br>
     <p style ="text-align: justify;font-size: 10.5px;">El EMPLEADOR y TRABAJADOR en adelante se las denominará conjuntamente como “Partes” e individualmente como “Parte”.</p>
     <br>
     <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">PRIMERA. OBJETO DEL CONTRATO:</p>
     <?php if($datos['id_sys_adm_area'] == 2):?>
     <p style ="text-align: justify;font-size: 10.5px;">El EMPLEADOR para el cumplimiento de sus actividades y por la necesidad de aumentar la demanda de producción de su actividad necesita contratar los servicios laborales de un <span style="font-weight: bold;">TRABAJADOR DE PRODUCCION</span>, revisados los antecedentes del (de la) 
     señor(a)(ita) <span style="font-weight: bold;"><?= $datos['nombres'] ?></span>, éste(a) declara tener los conocimientos necesarios para el desempeño del cargo indicado, por lo que con base a las consideraciones anteriores y por lo expresado en los numerales siguientes, el EMPLEADOR y el TRABAJADOR (a) proceden a celebrar el presente Contrato de Trabajo.</p>
     <?php endif; ?>
     <br>
     <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">SEGUNDA.-  JORNADA ORDINARIA Y HORAS EXTRAORDINARIAS.</p>
     <p style ="text-align: justify;font-size: 10.5px;">El TRABAJADOR  prestará sus servicios conforme el Art. 47 del Código de Trabajo, es decir, la jornada máxima de trabajo será de ocho horas diarias, laborará de lunes a viernes en el horario de <?= date('H:i',strtotime($datos['hora_inicio'])) ?> a  <?= date('H:i',strtotime($datos['hora_fin'])) ?>, con descanso de 0h30 hora de almuerzo desde las 12h30 a 13h00 (de acuerdo al artículo 57 del mismo cuerpo legal), de manera que no exceda de cuarenta horas semanales, salvo disposición de la ley en contrario.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;">El TRABAJADOR declara conocer y aceptar la jornada laboral establecida, sin perjuicio de lo cual, por mutuo acuerdo con EL EMPLEADOR se compromete a laborar horas suplementarias y/o extraordinarias, así como en horarios especiales de conformidad con la ley.  En todo caso, para efectos de liquidación y pago de remuneraciones, las partes convienen que el horario de trabajo  y las horas laboradas solo serán acreditadas de acuerdo a los registros existentes de la empresa.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;">El horario de labores podrá ser modificado por el empleador cuando lo estime conveniente y acorde a las necesidades y a las actividades de la empresa, siempre y cuando dichos cambios sean comunicados con la debida anticipación, conforme el artículo 63 del Código del Trabajo. </p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">TERCERA.- REMUNERACIÓN.-</p>
     <p style ="text-align: justify;font-size: 10.5px;">El EMPLEADOR pagará al TRABAJADOR (a) por la prestación de sus servicios la remuneración convenida de mutuo acuerdo en la suma de <?php $V=new EnLetras(); $valor = $V->ValorEnLetras($datos['sueldo'],"d&oacute;lares");?><?= strtoupper($valor) ?> DOLARES DE LOS ESTADOS UNIDOS DE AMÉRICA 
     <?php
      $array = explode('.', trim($datos['sueldo']));
      $entero     = floatval($array[0]);
      $decimal     = floatval($array[1]);
 
      if($decimal != 0):
      ?>
        CON <?= $decimal?>/100 
      <?php endif; ?>
      (USD$<span style="font-weight: bold;"><?= $datos['sueldo']?></span>) mensuales.  </p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;">El EMPLEADOR reconocerá también al TRABAJADOR las obligaciones sociales y los demás beneficios establecidos en la legislación ecuatoriana, ya sea de forma mensualizada o según solicitud de acumulación.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">CUARTA.- DURACIÓN DEL CONTRATO.-</p>
      <p style ="text-align: justify;font-size: 10.5px;">El presente contrato tendrá una duración de un año, mientras dure la ejecución de su labor hasta el <span style="font-weight: bold;"><?= date("d", strtotime($datos['fecha_contrato']))?> de <?= $meses[date("n", strtotime($datos['fecha_contrato']))]?> del <?= date("Y", strtotime($datos['fecha_contrato']."+1 year"))?>.</span></p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;">Este contrato podrá terminar por las causales establecidas en el Art. 169 del Código de Trabajo en cuanto sean aplicables para este tipo de contrato.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">QUINTA.- LUGAR DE TRABAJO.-</p>
      <p  style ="text-align: justify;font-size: 10.5px;">El TRABAJADOR (a) desempeñará las funciones para las cuales ha sido contratado en las instalaciones ubicadas en  Km. 1-1/2 Vía Montecristi - Guayaquil entrada al Sitio Los Bajos, en la ciudad de Montecristi, provincia de Manabí, para el cumplimiento cabal de las funciones a él encomendadas.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">SEXTA.- OBLIGACIONES DE LOS TRABAJADORES Y EMPLEADORES.-</p>
      <p  style ="text-align: justify;font-size: 10.5px;">En lo que respecta a las obligaciones, derechos y prohibiciones del empleador y trabajador, estos se sujetan estrictamente a lo dispuesto en el Código de Trabajo en su Capítulo IV “De las obligaciones del Empleador y del Trabajador”, al Reglamento Interno de Trabajo, Reglamento de Seguridad y Salud Ocupacional, 
      políticas internas de la empresa, a más de las estipuladas en este contrato, y por tanto suficientes para dar por terminado la relación laboral.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">SEPTIMA.- CONDICIONES.-</p>
      <p  style ="text-align: justify;font-size: 10.5px;">El CONTRATADO, además, se obliga a cumplir con las siguientes funciones generales:<br> 
      1.- Prestar sus servicios en forma personal y exclusiva al EMPLEADOR de acuerdo a sus reglamentos, manual de funciones, intructivos, órdenes e instrucciones que dicte, empleando siempre el respeto, cuidado y diligencia necesarios, cumpliendo las expectativas para el cargo para el cual fue contratado en beneficio de empresa. 2.- Guardar estrictamente las normas de reserva, 
      conducta y buena educación con sus superiores y demás personal del EMPLEADOR, y en particular con las personas con las que debe tratar en razón de su actividad, 3.- Concurrir puntualmente al lugar de trabajo en condiciones óptimas para el trabajo, 4.- En general, cumplir con los deberes y obligaciones en la Ley de la materia. El incumplimiento de cualquiera de las estipulaciones 
      establecidas en el presente contrato será causa suficiente para dar por terminado de inmediato el presente contrato de trabajo, previo el trámite dispuesto por la Ley.
      </p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">OCTAVA.- OTRAS ESTIPULACIONES.-</p>
      <p  style ="text-align: justify;font-size: 10.5px;"> El Trabajador declara de manera expresa y formal que se obliga a realizarse los chequeos médicos establecidos en el Reglamento para el Sistema de Auditoría del Riesgos del Trabajo aplicables y que estén vigentes y emitidos por los entes competentes. Ante la negativa de El Trabajador a realizarse los chequeos médicos ocupacionales 
      (periódicos, al reintegro y especiales) y el post-ocupacional, este declara que deslinda a la Empleadora de toda responsabilidad que pudiere llegar a producirse por la no realización de estos, incluyendo, pero no limitándose, ante el Instituto Ecuatoriano de Seguridad Social y demás entes competentes.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">NOVENA. – CONFIDENCIALIDAD.-</p>
      <p  style ="text-align: justify;font-size: 10.5px;"> Los descubrimientos e invenciones, las mejoras en los procedimientos, así como los trabajos y resultados de las actividades de EL TRABAJADOR, mientras preste sus servicios a la EMPLEADORA, quedarán de la propiedad exclusiva de ésta, la cual podrá patentar o registrar a su nombre tales inventos o mejoras. </p>
      <br>
      <p  style ="text-align: justify;font-size: 10.5px;">El trabajador reconoce expresamente que la información que obtenga como consecuencia de la ejecución de su trabajador es de propiedad exclusiva de la empleadora. </p>
      <br>
      <p  style ="text-align: justify;font-size: 10.5px;">EL TRABAJADOR reconoce que durante el tiempo que preste sus servicios en la Empresa podrá recibir información confidencial valiosa, tanto de carácter técnico, como comercial, laboral, entre otras, la misma que podrá obtener de manera verbal, visual, por escrito o por cualquier otra forma tangible o intangible; 
      EL TRABAJADOR acepta tratar toda esta información como confidencial (secreta) y expresamente se prohíbe al TRABAJADOR su divulgación, bajo cualquier medio y se obliga a tomar todas las precauciones necesarias contra la divulgación de dicha información a terceros durante y después de la vigencia de este contrato. </p>
      <br>
      <p  style ="text-align: justify;font-size: 10.5px;">En caso de incumplimiento de la presente cláusula por parte del TRABAJADOR, PESPESCA S.A. tendrá derecho a aplicar las sanciones determinadas en el Reglamento Interno de Trabajo y será causal para dar por terminado el presente contrato conforme lo determinado en el Código de Trabajo,  sin perjuicio de reclamar ante las instancias 
      judiciales competentes y a obtener la indemnización por los daños y perjuicios que tal divulgación y uso no autorizado le hayan generado.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">DÉCIMA. - HORARIOS ESPECIALES DE TRABAJO:</p>
      <p style ="text-align: justify;font-size: 10.5px;">Mediante resoluciones MDT-DRTSP4-2021-0390-R1-KM y MDT-DRTSP4-2022-0417-R1-KM., fueron aprobadas solicitudes de horarios de trabajo especiales, los cuales podrán ser aplicables por parte del TRABAJADOR en el caso que la EMPLEADORA así lo considere pertinente.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">DÉCIMA PRIMERA.- LEGISLACIÓN APLICABLE.- </p>
      <p style ="text-align: justify;font-size: 10.5px;">En todo lo no previsto en este Contrato, cuyas modalidades especiales las reconocen y aceptan las partes, éstas se sujetan a la Ley Orgánica de Apoyo humanitario en su Art. 19 y al Código del Trabajo.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">DÉCIMA SEGUNDA.- NOTIFICACIONES.- </p>
      <p style ="text-align: justify;font-size: 10.5px;">Las partes señalan las siguientes direcciones electrónicas para efecto de recibir cualquier tipo de las notificaciones y/o comunicaciones que le corresponda:</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">EMPLEADOR</p>
      <p style ="text-align: justify;font-size: 10.5px;">Correo electrónico: legal@pespesca.com</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">TRABAJADOR</p>
      <p style ="text-align: justify;font-size: 10.5px;">Correo electrónico:<?= $datos['email'] ?></p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;">Adicionalmente, EL TRABAJADOR, de manera libre y voluntaria, autoriza expresamente recibir en la dirección de correo electrónica antes señalada, sus roles de pagos mensuales, cuya recepción implica una aceptación 
      expresa de los valores recibidos por la prestación de sus servicios.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;"><span style="font-weight: bold;">DÉCIMA TERCERA.- COGEP.- <i>“Art. 55.-</i></span><i>Citación por boletas.-…A quien no se les pueda encontrar personalmente o cuyo domicilio o residencia sea imposible determinar previo a citar 
      por la prensa, se le podrá citar de forma telemática por boletas bajo las siguientes reglas: 2. A las personas naturales o jurídicas, cuando en un contrato conste la aceptación clara y expresa para ser citados por ese medio y la dirección de correo electrónico correspondiente.” </i></p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">DÉCIMA CUARTA.- JURISDICCIÓN Y COMPETENCIA.-</p>
      <p style ="text-align: justify;font-size: 10.5px;">En caso de suscitarse discrepancias en la interpretación, cumplimiento y ejecución del presente Contrato y cuando no fuere posible llegar a un acuerdo amistoso entre las Partes, estas se someterán a los jueces competentes determinados por la Ley.</p>
      <br>
      <p style ="text-align: justify;font-size: 10.5px;font-weight: bold;">DÉCIMA  QUINTA.- SUSCRIPCIÓN.-</p>
      <p style ="text-align: justify;font-size: 10.5px;">Las partes se ratifican en todas y cada una de las cláusulas precedentes y para constancia y plena validez de lo estipulado firman este contrato en original y dos ejemplares de igual tenor y valor, en la ciudad de Montecristi a los <?= date("d", strtotime($datos['fecha_ingreso']))?> días del mes de <?= $meses[date("n", strtotime($datos['fecha_ingreso']))]?> del año <?= date("Y", strtotime($datos['fecha_ingreso']))?>.</p>
      <br>
      <br>
      <br>
    </div>
    <div class = "col-md-12 margen-left">
      <table>
        <tr>
          <td><p style ="text-align: left;font-size: 10.5px;"><span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          EMPLEADOR</span><br><p>MEZA CHICA MARIA FERNANDA<br><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C.C 131101751-9</p></p></p></td>
          <td style ="text-align: right"><p style ="text-align: right;font-size: 10.5px;"><span style="font-weight: bold;">
          TRABAJADOR</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><p><?= $datos['nombres'] ?><br><p>C.C <?= $datos['id_sys_rrhh_cedula'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></p></p></td>
        <tr>
      </table>
    </div>
</div>

<?php
class EnLetras
{
var $Void = "";
var $SP = " ";
var $Dot = ".";
var $Zero = "0";
var $Neg = "Menos";
 
function ValorEnLetras($x )
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
 
$s = $s ;
 

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