<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use app\models\SysAdmAreas;
use app\models\SysAdmPeriodoVacaciones;
use kartik\depdrop\DepDrop;
$this->title = 'Informe Periodo Vacaciones';?>


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
          <div class= 'col-md-2 col-md-offset-2'>
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
 
               ?>
               
           </div>
           <div class = 'col-md-2'>
              <?php 
                echo '<label>Periodo</label>';
                echo  Html::DropDownList('periodo', 'periodo',
                      ArrayHelper::map(SysAdmPeriodoVacaciones::find()->where(['id_sys_empresa'=> '001'])->andWhere(['<=','anio_vac_hab', date('Y')])->all(), 'id_sys_adm_periodo_vacaciones', 'periodo'), ['class'=>'form-control input-sm', 'id'=>'departamento', 'prompt' => 'Todos',  'options'=>[ $periodo => ['selected' => true]]])
  
              ?>
           </div>
           <div class = 'col-md-2'>
              <?php 
                echo '<label>Estado</label>';
                echo  Html::DropDownList('estado', 'estado',
                   ['P'=> 'Pendiente', 'T'=> 'Tomadado'], ['class'=>'form-control input-sm', 'id'=>'estado', 'prompt' => 'Todos',  'options'=>[$estado => ['selected' => true]]])
  
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
      <div class= 'row'>
          <div class = 'col-md-12'>
            <?=  Html::a('Exportar a PDF', ['infoperiodovacacionespdf','area'=> $area, 'departamento'=> $departamento, 'periodo'=> $periodo, 'estado'=> $estado], ['class'=>'btn btn-xs btn-danger pull-right' ]);?>
          </div>
      </div>
      <br>
      <div class= 'row'>
         <div class= 'col-md-12'>
             <?php echo $this->render('_tableperiodovacaciones', ['datos'=> $datos]) ?>
         </div>
      </div>
  <?php endif; ?>
 