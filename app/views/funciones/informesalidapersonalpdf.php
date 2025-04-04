<?php
/* @var $this yii\web\View */
use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
?>
<?php if($datos): ?>

</style>
 <div class='row'>
        <div class='col-xs-12 text-center'>
            <img src="<?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height='50' width='140'>
         </div>
		<div class='col-xs-12'>
			<h6 class='subtitle text-center'><b>Informe de Salida  Personal</b></h6>
			<h6 class='subtitle text-center'><b><?= "Desde ".$meses[$mesini]." Hasta ".$meses[$mesfin]." del ".$anio?></b></h6>
		</div>
 </div>
  <div class= 'row' >
       <?=  $this->render('_tablesalidapersonal', [ 'mesini'=> $mesini, 'datos'=> $datos, 'anio'=> $anio]);?>
  </div>
<?php endif;?> 

