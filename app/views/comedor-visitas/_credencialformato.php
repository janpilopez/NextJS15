<?php

use app\models\SysEmpresa;
use yii\bootstrap\Html;
$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
$empresa = SysEmpresa::find()->where(['id_sys_empresa'=> '001'])->one();

$fechavecimiento = '';

if($empresa):
   
    if($empresa->vencimiento_credencial):
           $fechavecimiento =  $empresa->vencimiento_credencial;
    else:
           $fechavecimiento  = date('Y-m-d');    
    endif;
   
else:
   
          $fechavecimiento  = date('Y-m-d');    

endif;

$db =  $_SESSION['db'];

$empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();


?>
<style>

 .credencial-formato {
     background-size:contain !important;
     width: 180px !important;
     height: 275px !important;
     padding: 3px !important;
     display: inline-block !important;
     margin: 1px;
     
  }
  
    .credencial-formato > div {
       padding : 1px !important;
   }
   
   .cabecera { 
     width: auto !important;
     height: 30% !important;
    }
   
    .datos-departamento { 
	  width: auto !important;
      height: 15% !important;   
    }
    .departamento{
      
        width:99% !important;
        height:100% !important;
        display: inline-grid !important;
	    justify-items: center !important;
	    align-items: center !important;
        text-align: center !important;
    }
  
      .datos-comedor { 
    	  width: auto !important;
          height: 18% !important;
          text-align: center !important;
         
    	}
    	
    	.codigo-comedor {
            padding : 3px !important;
            width:160px !important;
            height:45px !important;
        }
        
        .footer{
    	   width: auto !important;
           height: 3% !important;
    	}
    	
        .footer-credencial {
    	  width: auto !important;
          height: 3% !important;
          padding: 2px !important;
          display: grid;
          grid-template-columns: 50% 48% !important;
    	}
	
	  .leyenda{
	  
        font-size: 5px !important;
        color: red !important; 
        
       }
        
	  .alinear-derecha {
    
         text-align: right !important;
    
       }
       
      .alinear-izquierda {
    
         text-align: left !important;
    
      }
</style>
<div class="credencial-formato"  style ="background-image: url('<?=Yii::$app->homeUrl."logo/".$empresa->ruc."/".$empresa->credencial.""?>') !important;">
  <div class= "cabecera"></div>
   <div class= "datos-departamento">
    <div class = "departamento" style= "font-weight: bold !important;">
             <?= $datos['departamento']?>
    </div>
  </div>
  <div class= "datos-departamento">
    <div class = "departamento" style= "background-color: #99bbff !important; color:#ffffff !important;font-size:12px !important; font-weight: bold !important;">
             VISITAS 
    </div>
  </div>
  <div class = "datos-comedor">
        <?php 
    	    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    	    echo  Html::img('data:image/png;base64, '.base64_encode($generator->getBarcode(trim($datos['codigo']), $generator::TYPE_CODE_39)), ["class"=> 'codigo-comedor']);
    	 ?>
   </div>
   <div class = "footer-credencial">
          <div class = "leyenda alinear-izquierda">  Fecha de caducidad: <?= $fechavecimiento?> </div>
          <div class = "leyenda alinear-derecha">www.<?= strtolower($empresa->razon_social)?>.com</div>
     </div>
 </div>