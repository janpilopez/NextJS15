<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use app\assets\AppAsset;
use app\models\SysEmpresa;
AppAsset::register($this);
$empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();


if($datos):?>
 <div class="row">
        <div class="col-xs-12 text-center">
            <img src="<?= Yii::getAlias('@webroot')."/".trim("logo/".$empresa->ruc."/".$empresa->logo)?>"  height="30" width="120">
         </div>
		<div class="col-xs-12">
			<h4 class="subtitle text-center"><b>Entrega Canastas Navideñas - Año <?= $anio?></b></h4>
		</div>
 </div>
  <div class= 'row' >
       <?=  $this->render('_tablecanastas', ['datos'=> $datos]);?>
  </div>
  
<?php endif;?> 






