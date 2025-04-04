<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */


use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);


//listado de funciones de calculos
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

?>
  <div class="row">
        <div class="col-xs-12 text-center">
             <img src="<?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="50" width="140">
         </div>
		<div class="col-xs-12">
			<h3 class="subtitle text-center" style="text-decoration: underline; margin-top: 0px;"><b>Informe Individual Visitas</b></h3>
		</div>
 </div>
 <div class="row" style="border: 1px solid black; margin-bottom: 5px; border-radius: 5px;">
    	  <table style="font-size: 10px; width: 100%">
    	      <tr>
    	         <td width= '15%'><b>Nombres :</b></td><td width='35%'><?= $empleado->nombres?></td><td width='15%' ><b>Cédula :</b></td><td width='35%'><?= $empleado->id_sys_rrhh_cedula?></td>
    	      </tr>
    	      <tr>
    	        <td width= '15%'><b>Desde :</b></td><td width='35%'><?= $fechaini?></td><td width='15%' ><b>Hasta :</b></td><td width='35%'><?= $fechafin?></td>
    	      </tr>
    	  </table>
 </div>
 <div class= "row">
        <div class = 'col-md-12'>
                  <?php  echo $this->render('_tablevisitasindividual', ['datos'=> $datos,'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 10px; width: 100%"]);?>
            </div>
 </div>







