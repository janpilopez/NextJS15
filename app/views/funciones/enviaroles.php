<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use app\models\SysAdmAreas;
use kartik\depdrop\DepDrop;
$url = Yii::$app->urlManager->createUrl(['funciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);
use app\assets\EnviarolesAsset;
EnviarolesAsset::register($this);
$this->title = 'Enviar Roles';
$this->render('../_alertFLOTADOR'); 
$meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
?>


<style>
#tablemodal_filter > label{
    width:100%;
}
.table__no-margin{
    margin-bottom: 0px;
}
tr.seleccion{
	background-color: #9ec4e2;
}
</style>
<div class="site-index">
  <h1><?= Html::encode($this->title)?></h1>
   <?php $form = ActiveForm::begin(); ?>
   <div class= 'row'>
       <div class= 'col-md-2'>
             <label >Año</label>
             <input type="number" class="form-control input-sm" value = "<?= $anio?>" name = 'anio' id= 'anio'>
       </div>
       <div class = 'col-md-2'>
          <?php 
             echo '<label>Mes</label>';
             echo  Html::dropDownList('mes', 'mes', $meses,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 'id'=> 'mes',
                   'options' =>[ 'id'=>'mes',$mes => ['selected' => true]]
                  ]);
               ?>
       </div>
       <div class= 'col-md-2'>
            <?php
             echo '<label>Periodo</label>';
             echo  Html::dropDownList('periodo', 'periodo', $periodos,
                 ['class'=> 'form-control input-sm', 'prompt' => 'Seleccione..', 'id'=> 'periodo',
                     'options' =>[$periodo => ['selected' => true]]
                     
                 ]);
             ?>
       </div>
          <div class= 'col-md-2'>
             <?php echo '<label>Area</label>';
                   echo   Html::DropDownList('area', 'area', 
                       ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos', 'options'=>[ $area => ['selected' => true]]])
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
                  
                   /*echo  Html::DropDownList('departamento', 'departamento',
                         ArrayHelper::map(SysAdmDepartamentos::find()->andFilterWhere(['id_sys_adm_area'=> $area])->all(), 'id_sys_adm_departamento', 'departamento'), ['class'=>'form-control input-sm', 'id'=>'departamento', 'prompt' => 'Todos',  'options'=>[ $departamento => ['selected' => true]]])
                  */
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
  <?php if($datos):?>
   
   <?= $this->render('_tableempleados', ['datos'=> $datos])?>
   
 
  <?php endif; ?>
  
<div id = "loading"></div>