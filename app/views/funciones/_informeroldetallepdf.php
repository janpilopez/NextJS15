<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;;
use app\models\SysEmpresa;
use app\assets\AppAsset;
use app\models\SysRrhhEmpleadosRolCab;
AppAsset::register($this);

$objarea =  SysAdmAreas::find()->where(['id_sys_adm_area'=> $area])->one();
$objdepar = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $departamento])->one();
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
$rol  = SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->one();

$dia = date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));

$dia = $dia > 30 ? '30': $dia;


$tipo = '';

if($periodo == 1 ): 

   $tipo = 'Quincenal';

elseif($periodo == 2 ) :

   $tipo = 'Mensual';

elseif($periodo ==  90):

   $tipo = 'Beneficios';  

elseif($periodo == 70):    
   
   $tipo = 'Dec. Tercero';
 
elseif($periodo == 71):
     
   $tipo = 'Dec. Cuarto';

endif;

$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
?>
<div class="site-index">

 <div class="row">
        <div class="col-xs-12 text-center">
           <img src=" <?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>" height="50" width="140">
         </div>
		<div class="col-xs-12"> 
			<h6 class="subtitle text-center"><b>Rol de Pagos <?= $objarea== null ? 'Todos': $objarea->area?></b></h6>
		</div>
 </div>
 <div class = "row">
     <div style="border: 1px solid #000; padding: 5px; margin-bottom: 5px; border-radius: 5px;">
    	  <table  style="background-color: white; font-size: 11px; width: 90%">
    	      <tr>
    	         <td><b>Año: </b> <?= $anio?></td><td><b>Mes: </b><?= $meses[$mes] ?></td><td><b>Tipo Rol:</b> <?= $tipo ?></td><td><b>Fecha Inicio: </b><?= $rol->fecha_ini?></td><td><b>Fecha Final: </b><?= $rol->fecha_fin ?></td>
    	      </tr>
    	  </table>
       </div>
 </div>
   <div class= "row">
       <?=  $this->render('_tableroldetalle', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);?>
  </div>
 </div>






