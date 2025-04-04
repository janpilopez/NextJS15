<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysEmpresa;
use app\assets\AppAsset;
AppAsset::register($this);

$meses      =  Yii::$app->params['meses'];
$periodos   = Yii::$app->params['periodos'];

$dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));

$dia = $dia > 30 ? '30': $dia;

$mjs = "";


if($periodo == 1 ): 

$mjs  = 'ANTICIPOS POR PAGAR';


elseif($periodo == 2 ) :

$mjs   = 'SUELDOS POR PAGAR';

elseif($periodo == 70):    
   
$mjs   = 'DECIMO TERCERO POR PAGAR';
 
elseif($periodo == 71):
     
$mjs   = 'DECIMO CUARTO POR PAGAR';

elseif($periodo == 72):

$mjs   = 'UTILIDADES';

endif;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

?>
<div class="site-index">
 <div class="row">
        <div class="col-xs-12 text-center">
             <img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>"height="50" width="140">
         </div>
		<div class="col-xs-12">
			<h3 class="subtitle text-center" style="text-decoration: underline; margin-top:0"><b><?= $tipopago == 'C'? 'LISTADO DE CHEQUES': 'LISTADO DE TRANSFERENCIAS'?></b></h3>
		</div>
 </div>
 <div class = "row">
     <div style="border: 1px solid #000; padding: 3px; margin-bottom: 5px; border-radius:5px;">
    	  <table  style="background-color: white; font-size: 12px; width: 90%">
    	      <tr>
    	         <td  height="0px; !important"><b>AÑO : </b> <?=  $anio?></td>
    	         <td><b>MES : </b><?= strtoupper($meses[$mes]) ?></td>
    	         <td><b>TIPO ROL :</b> <?= strtoupper($periodos[$periodo]) ?></td>
    	      </tr>
    	  </table>
       </div>
 </div>
   <div class= "row">
       <?=  $this->render('_tablestipopago', ['datos'=> $datos, 'estilo'=> 'background-color: white; font-size: 10px; width: 100%;']);?>
  </div>
 </div>






