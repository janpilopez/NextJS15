<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysEmpresa;
use app\assets\AppAsset;
AppAsset::register($this);

$holgura  =  15;

$objarea  =  SysAdmAreas::find()->where(['id_sys_adm_area'=> $area])->one();
$objdepar = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $departamento])->one();

$empresa  = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

$reporte  = "Resumen Horas Laboradas";


?>
 <div class="row">
        <div class="col-xs-12 text-center">
               <img src="<?=Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="30" width="120">
         </div>
		<div class="col-xs-12">
			<h3 class=" text-center" style="margin: 2px; text-decoration: underline;"><b><?= $reporte?></b></h3>
		</div>
 </div>
 <div class = "row">
       <div class="col-xs-12" style="border: 1px solid black; border-radius: 5px;">
    	  <table style="font-size: 12px; width: 100%">
    	      <tr>
    	         <td width= '13%'><b>Área:</b></td><td width='35%'><?= $objarea== null ? 'Todos': $objarea->area?></td><td width='15%' ><b>Departamento:</b></td><td width='35%'><?=  $objdepar== null ? 'Todos': $objdepar->departamento ?></td>
    	      </tr>
    	      <tr>
    	        <td width= '13%'><b>Fecha Inicio :</b></td><td width='35%'><?= $fechaini?></td><td width='15%' ><b>Fecha Final:</b></td><td width='35%'><?= $fechafin?></td>
    	      </tr>
    	  </table>
      </div>
 </div>
  <br>
 <div class= "row">
    <div class="col-xs-12">
     <?= $this->render('_tableausentismo', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin,  'style' => "background-color: white; font-size: 9px; width: 100%"]);?>
    </div>
 </div>