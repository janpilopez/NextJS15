<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
$this->title = 'Listado de Prestamos Últimos 3 años';
?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>


  <?php if($datos): ?>
  <div class ="row" >
      <div class="col-md-12">
        <?=  Html::a('Exportar a Excel', ['prestamos2xls'], ['class'=>'btn btn-xs btn-success pull-right' ]);?>
       </div>
  </div>
  <br>
  <div class= 'row' >
       <?=  $this->render('_tableprestamos2', ['datos'=> $datos]);?>
  </div>
  <?php endif;?> 

</div>