<?php

use app\assets\AppAsset;
use yii\bootstrap\Html;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
AppAsset::register($this);
$generator = new Picqer\Barcode\BarcodeGeneratorHTML();

$db =  $_SESSION['db'];
$empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();


$fechavecimiento = '';

//Validacion de fuente de nombres y apellidos 
$longitudnombres =strlen($datos['nombre']);
$longitudapellidos = strlen($datos['apellidos']);

if($longitudapellidos > $longitudnombres):
  
   $longitud = $longitudapellidos;

else:

   $longitud = $longitudnombres;

endif;
    
$pixelsdatos = 0.04;
$fuente = 9.5;
$numerocaracteres =20;


if($longitud > $numerocaracteres):

    $reajuste = ($longitud - $numerocaracteres) * $pixelsdatos;
    $fuente = $fuente - $reajuste;
  
endif;



if($empresa):
   
    if($empresa->vencimiento_credencial):
          $fechavecimiento =  $empresa->vencimiento_credencial;
    else:
           $fechavecimiento  = date('Y-m-d');    
    endif;
   
else:
   
          $fechavecimiento  = date('Y-m-d');    

endif;



$cargo =  (new \yii\db\Query())
->select(["nivel"])
->from("sys_adm_cargos")
->innerJoin('sys_adm_mandos','sys_adm_cargos.id_sys_adm_mando = sys_adm_mandos.id_sys_adm_mando')
->where("sys_adm_cargos.id_sys_empresa = '001'")
->andwhere("id_sys_adm_cargo = '{$datos['id_sys_adm_cargo']}'")
->one(SysRrhhEmpleados::getDb());


$colordepartamento = '';
$colorfuente = '';

if($cargo['nivel'] < 2):

  $colordepartamento = '#FFFFFF';
  $colorfuente = '#092b4a';

else:
    
  $colordepartamento = $datos['color'];
  $colorfuente = $datos['color_fuente'];
endif;

//FOTO DEL EMPLEADOR

$fotos =    Yii::$app->$db->createCommand("select foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$datos['id_sys_rrhh_cedula']}'")->queryOne();
?>
<div class="credencial-formato" style ="background-image: url('<?=Yii::$app->homeUrl."logo/".$empresa->ruc."/".$empresa->credencial.""?>')!important;">
     <div class = "cabecera"></div>
     <div class = "datos-empleados-foto">
     		<?php 
     		  if ($fotos['baze64'] != null) :
            echo  Html::img('data:image/jpeg;base64, '.$fotos['baze64'], ['class'=>'foto']);
          else :
            echo  Html::img(Yii::$app->homeUrl."img/sin_foto.jpg", ['class'=>"foto"]);
          endif;
        ?>
     	</div>
      <div class = "datos-empleado">
     		<div class = "datos-empleados-identificacion">
            <div class= "datos-personales">
              <div class= "datos-personales-item"><?=$datos['apellidos']?></div>
            </div>
     		    <div class= "datos-personales">
     		      <div class= "datos-personales-item"><?=$datos['nombre']?></div>
     		    </div>
            <?php 

            if(strlen($datos['departamento']) > 30):
            ?>
            <div class= "datos-personales-departamento" >
     		    	<div style= "font-size:10px !important;font-weight: bold !important; color: #202853 !important;"><?= $datos['departamento']?></div>
     		    </div>
             <div class= "datos-personales" >
     		    	<div style= "font-size:10px !important;">CI :<?= $datos['id_sys_rrhh_cedula']?></div>
     		    </div>
     		    <div class= "datos-personales">
     		      <div style= "font-size:10px !important;">FACTOR RH : <?= $datos['tipo_sangre']?></div>
     		    </div>
            <div class="datos-empleados-qr">
              <img height="60px" width="60px" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=pespesca.com/es/ce/contact/ PESPESCAS.A.<?= $datos['barra']?>">
            </div>
            <?php
            else:
            ?>
            <div class= "datos-personales-departamento-30" >
     		    	<div style= "font-size:10px !important;font-weight: bold !important; color: #202853 !important;"><?= $datos['departamento']?></div>
     		    </div>
             <div class= "datos-personales" >
     		    	<div style= "font-size:10px !important;">CI :<?= $datos['id_sys_rrhh_cedula']?></div>
     		    </div>
     		    <div class= "datos-personales">
     		      <div style= "font-size:10px !important;">FACTOR RH : <?= $datos['tipo_sangre']?></div>
     		    </div>
             <div class="datos-empleados-qr-2">
             <img height="65px" width="65px" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=pespesca.com/es/ce/contact/ PESPESCAS.A.<?= $datos['barra']?>">
            </div>
            <?php
            endif;
            ?>
     		</div>
      </div>
      <div class = "footer-credencial">
          <div class = "leyenda-2 alinear-derecha">Telf: (05) 5000-002</div>
          <div class = "leyenda alinear-derecha"><?= $fechavecimiento?></div>
     </div>
</div>