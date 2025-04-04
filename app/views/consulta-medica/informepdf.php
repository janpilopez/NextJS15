<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */;
use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);
$empresa  = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
?>
 <div class="row">
        <div class="col-xs-12 text-center">
              <img src="<?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>"  height="50" width="140">
         </div>
		<div class="col-xs-12">
			<h3 class=" text-center" style="margin: 2px; text-decoration: underline;"><b>Historial  de Atenciones Médicas - Dpto. Médico</b></h3>
		</div>
	  <div class="col-xs-12">
			<h4 class=" text-center" style="margin: 2px;"><b>Desde <?= $fechaini?> Hasta <?= $fechafin?></b></h4>
		</div>
 </div>
  <br>
 <div class= "row">
    <div class="col-xs-12">
    	<?php  echo $this->render('_tableinforme', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'style' => "background-color: white; font-size: 7px; width: 100%"]);?>
    </div>
 </div>







