<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */;
use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);
$empresa  = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
$meses = [1 => 'ENERO',  2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE' ];

?>
 <div class="row">
  <table style = "width: 100%; font-size: 11px;" border="1">
       <tr> 
         <td width="70%" class="negrita text-center title">DEPARTAMENTO MÉDICO</td>
         <td rowspan="3" width="30%"  class="text-center">
         	<img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="40" width="180" class ="text-center">
         </td>
       </tr>
       <tr>
         <td class="negrita text-center title">REPORTE DE CERTIFICADOS DE SALUD VENCIDOS</td>
       </tr>
       <tr>
        <td class="negrita text-center subtitle">GENERADO HASTA <?=$meses[$mes]?> <?=$anio?></td>
       </tr>
    </table>
 </div>
  <br>
 <div class= "row">
    <div class="col-xs-12">
    	<?php  echo $this->render('_tablecertificadosvencidos', ['datos'=> $datos, 'style' => "background-color: white; font-size: 8px; width: 100%"]);?>
    </div>
 </div>







