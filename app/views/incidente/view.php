<?php
use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
$turno = "";
switch ($model->turno) {
    case "M":
        $turno = "MATUTINO";
        break;
    case "V":
        $turno = "VESPERTINO";
        break;
    case "N":
        $turno = "NOCTURNO";
        break;
}
?>
<table style = "width: 100%; font-size: 11px;" border="1">
   <tr>
     <td rowspan="2" width="20%">
     	<img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="25" width="120" class ="text-center">
     </td>
     <td width="60%" class="negrita text-center title">DEPARTAMENTO DE SEGURIDAD Y SALUD OCUPACIONAL</td>
     <td width="20%" rowspan="2" class="text-center"><?= str_pad($model->secuencial, 10, "0", STR_PAD_LEFT) ?></td>
   </tr>
   <tr>
     <td class="negrita text-center title">REPORTE INTERNO DE INCIDENTES</td>
   </tr>
</table>
<table style = "width: 100%; font-size: 11px;" border="1">
   <tr>
     <td width="35%" class="title negrita">FECHA:</td>
     <td width="10%"><?= date('d/m/Y', strtotime($model->fecha))?></td>
     <td width="15%" class="title negrita">HORA</td>
     <td width="10%"><?= date('H:i:s', strtotime($model->fecha))?></td>
     <td width="15%" class="title negrita">TURNO</td>
     <td width="15%"><?= $turno?></td>
   </tr>
   <tr>
     <td width="30%" colspan= "2" class="title negrita">ÁREA:</td>
     <td width="70%" colspan= "4"><?= $area->area?></td>
   <tr>
   <tr>
     <td width="30%" colspan= "2" class="title negrita">LUGAR DEL INCIDENTE:</td>
     <td width="70%" colspan= "4"><?=  $model->lugar?></td>
   <tr>
   <tr>
     <td width="30%" colspan= "2" class="title negrita">PUESTO DE TRABAJO:</td>
     <td width="70%" colspan= "4"><?= $model->puesto_trabajo?></td>
   <tr>
   <tr>
     <td width="30%" colspan= "2" class="title negrita">NOMBRE DE LA PERSONA IMPLICADA:</td>
     <td width="70%" colspan= "4"><?= $empleado->nombres?></td>
   <tr>
   <tr>
     <td width="30%" colspan= "2" class="title negrita">CEDULA DE INDENTIFICACIÓN:</td>
     <td width="70%" colspan= "4"><?= $empleado->id_sys_rrhh_cedula?></td>
   <tr>
   <tr style="background-color:#f2f2f2">
    <td width="100%" colspan= "6" class="title negrita text-center">EL INCIDENTE PRODUJO:</td>
   <tr>
   <tr>
     <td width="30%"  class="title">LESIÓN CORPORAL:</td>
     <td width="70%" colspan= "5"><?= $model->lesion_corporal?></td>
   <tr>
   <tr>
     <td width="30%"  class="title">LESIÓN A LA MAQUINARIA:</td>
     <td width="70%" colspan= "5"><?= $model->danio_maquinaria?></td>
   </tr>
   <tr>
     <td width="30%"  class="title">LESIÓN A LAS INSTALACIONES:</td>
     <td width="70%" colspan= "5"><?= $model->danio_instalaciones?></td>
   </tr>
   <tr>
     <td width="30%"  class="title">DAÑO AL EPP:</td>
     <td width="70%" colspan= "5"><?=  $model->danio_epp?></td>
    </tr>
    <tr>
     <td width="30%"  class="title">REPOSO/OBSERVACIÓN:</td>
     <td width="70%" colspan= "5"><?=  $model->observacion ?></td>
    </tr>
    <tr style="background-color:#f2f2f2">
       <td width="100%" colspan= "6" class="title negrita text-center">DESCRIPCIÓN DEL INCIDENTE:</td>
    </tr>
    <tr>
       <td width="100%"  colspan= "6" style="height:170px; vertical-align:baseline;"><?= nl2br($model->descripcion_incidente)?></td>
    </tr>
    <tr style="background-color:#f2f2f2">
       <td width="100%" colspan= "6" class="title negrita text-center">ANÁLISIS DEL PROBLEMA:</td>
    </tr>
    <tr>
       <td width="100%"  colspan= "6" style="height:70px; vertical-align:baseline;"><?= nl2br($model->analisis_problema) ?></td>
    </tr>
     <tr style="background-color:#f2f2f2">
       <td width="100%" colspan= "6" class="title negrita text-center">CORRECCIÓN:</td>
    </tr>
    <tr>
       <td width="100%"  colspan= "6" style="height:70px; vertical-align:baseline;"><?= nl2br($model->correcion) ?></td>
    </tr>
    <tr style="background-color:#f2f2f2">
       <td width="100%" colspan= "6" class="title negrita text-center">ACCIÓN PREVENTIVA:</td>
    </tr>
    <tr>
       <td width="100%"  colspan= "6" style="height:70px; vertical-align:baseline;"><?= nl2br($model->accion_preventiva) ?></td>
    </tr>
</table>
<table style = "width: 100%; font-size: 12px;" border="1">
   <tr>
     <td colspan="2" width="50%" class="text-center negrita">NOTIFICA EL INCIDENTE</td>
     <td colspan="2" width="50%" class="text-center negrita">COLOBORADOR QUE SUFRE EL INCIDENTE</td>
   </tr>
   <tr>
     <td width="10%">NOMBRE:</td>
     <td width="40%"><?= $model->notifica_incidente_nombre?></td>
     <td width="10%">NOMBRE:</td>
     <td width="40%"><?= $empleado->nombres?></td>
   </tr>
    <tr>
     <td width="10%">CARGO:</td>
     <td width="40%"><?= $model->notifica_incidente_cargo?></td>
     <td width="10%">CARGO:</td>
     <td width="40%"><?= $cargo->cargo?></td>
   </tr>
    <tr>
     <td width="10%">FIRMA:</td>
     <td width="40%" style="height:40px;"></td>
     <td width="10%">FIRMA:</td>
     <td width="40%" style="height:40px;"></td>
   </tr>
</table>
<br>
<br>
<br>
  <table style = "width: 100%; font-size: 12px; margin:0px !important; padding:0 !important;">
       <tr>
         <td width = "50%" class = "text-center">_________________________________</td>
         <td width = "50%" class = "text-center">_________________________________</td>
       <tr>
       <tr>
         <td width = "50%" class = "text-center">REALIZADO POR</td>
         <td width = "50%" class = "text-center">VERIFICADO POR</td>
       <tr>
    </table>
 <?php if($base64 != ""):?>
 <br>
 <table style = "width: 100%; margin:0px !important; padding:0 !important;">
    <tr style="background-color:#f2f2f2">
       <td width="100%" class="title negrita text-left">Adjunto:</td>
    </tr>
    <tr>
       <td style="height:170px; ">
           <img width="500px;" height ='200px;' src="<?= $base64?>" alt="" />
       </td>
    </tr>
 </table>
 <?php endif;?>