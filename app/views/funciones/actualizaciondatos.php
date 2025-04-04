<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\SysAdmAreas;
use yii\web\View;
use kartik\depdrop\DepDrop;
use app\assets\ActualizacionDatosAsset;
ActualizacionDatosAsset::register($this);

$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
$this->title = 'Actualización de datos anual';
$this->render('../_alertFLOTADOR'); 

$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];

?>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
      <div class= 'col-md-2 col-md-offset-3 text-center'>
            <label >Año</label>
            <input type="number" class="form-control input-sm" value = "<?=$anio?>" name = 'anio'>
      </div>
      <div class= 'col-md-2'>
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->andWhere(["estado"=>"A"])->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos', 'options'=>[ $area => ['selected' => true]]])
              ?>
       </div> 
       <div class = 'col-md-3'>
               <?php echo '<label>Departamento</label>';
                     echo DepDrop::widget([
                       'name'=> 'departamento',
                         'data'=> [$departamento => 'departamento'],
                       'options'=>['id'=>'departamento', 'class'=> 'form-control input-sm'],
                       'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                       'pluginOptions'=>[
                           'depends'=>['area'],
                           'initialize' => true,
                           'initDepends' => ['area'],
                           'placeholder'=>'Todos',
                           'url'=>Url::to(['/consultas/listadepartamento']),
                           
                       ]
                   ]);
               ?>
       </div>
   </div>
   <br>
   <div class ='row'>
   		 <div class = "form-group col-md-12 text-center">
          <button type="submit" class="btn btn-primary" id="btn-guardar">Consultar</button>
         </div>
   </div>
   
   <?php ActiveForm::end(); ?>
    
     

</div>
  <?php if($datos): ?>
  <div class= 'row' >
       <?=  $this->render('_tableactualizaciondatos', ['datos'=> $datos,'anio' => $anio]);?>
  </div>
  <?php endif;?> 

