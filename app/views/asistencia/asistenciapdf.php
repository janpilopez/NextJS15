<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use app\models\SysRrhhJornadasCab;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use app\assets\AgendamientoAsset;
use app\models\SysRrhhHorarioCab;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
AppAsset::register($this);
/*$url = Yii::$app->urlManager->createUrl(['agendamiento']);
$urlconsultas = Yii::$app->urlManager->createUrl(['consultas']);
$inlineScript = "var url='$url', urlconsultas = '$urlconsultas';";
$this->registerJs($inlineScript, View::POS_HEAD);

      <div class="col-xs-12 text-center">
           <?= Html::img('@web/logo/1391744064001/logo_reporte.jpg', ['style'=>"height='50'; width='500'"])?>
         </div>
		<div class="col-xs-12">
			<h2 class="title text-center"><b>Reporte de Asistencias</b></h2>
		</div>
		<div class="col-xs-6">

		<div class="col-xs-6">
*/
?>
<style>
  .titulo {
       font-size: 20px;
       font-weight: bold;
  }
  
  table, tr, td{
    
     margin:0px;
     padding:2px;
     border: 1px solid black;
  }
 
</style>
<div class="container">
     <div class="row">
       <div class = "col-xs-12">
          <table width="100%">
             <tr>
                 <td width="20%">
                  <?= Html::img('@web/logo/1391744064001/logo_reporte.jpg', ['style'=>"width: 110px; height: 60px;"])?>
                 </td>
                 <td width="60%" style="text-align: center;"><p class="titulo">Departamento de Desarrollo Organizacional - Informe de Asistencia</p></td>
                 <td width="20%">
                 <p>Fecha : <?php echo date('Y-m-d') ?></p>
                 <p>Hora : <?php echo date('H:i:s') ?></p>
                 </td>
             </tr>
          </table>
       </div>
    </div>
</div>
   
