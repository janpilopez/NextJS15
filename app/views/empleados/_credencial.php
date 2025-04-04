<?php

use yii\bootstrap\Html;
use app\assets\EmpleadosCredencialAsset;
EmpleadosCredencialAsset::register($this);
$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
?>
 <div class="principal">
      <?= $this->render('_credencialformato', ['datos'=> $datos])?>
 </div>