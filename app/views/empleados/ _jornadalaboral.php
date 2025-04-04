<?php
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use unclead\multipleinput\MultipleInputColumn;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use unclead\multipleinput\MultipleInput;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use app\models\SysRrhhEmpleadosJornada;
use app\models\SysRrhhJornadasCab;
use app\models\SysRrhhPermisos;
use kartik\select2\Select2;
use app\models\SysRrhhHorarioCab;
use app\models\SysRrhhEmpleadosHorario;


$cont  = 1;

if($update != 0):

  $cont =  SysRrhhEmpleadosHorario::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula, 'id_sys_empresa'=> $model->id_sys_empresa])->count();
  endif;
?>

<div class='row'> 
  <div class= 'col-md-12'>
  <?= Html::hiddenInput('horarios', $cont, ['id'=> 'datahorario']); ?>
  
   <div style="height: 300px; overflow: auto; font-size:11px;">
         <?php 
            echo  TabularInput::widget([
             
             'models' => $horarios,
             'id'=> 'horarios',
            /* 'attributeOptions' => [
                 'enableAjaxValidation'      => true,
                 'enableClientValidation'    => false,
                 'validateOnChange'          => false,
                 'validateOnSubmit'          => true,
                 'validateOnBlur'            => false,
             ],
             */
                
             'allowEmptyList' => true,
             'addButtonPosition' => MultipleInput::POS_HEADER,
             'addButtonOptions' => [
                 'class' => 'btn btn-xs btn-info',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>',
                 'id'    => 'addjornada'
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
                 'id'=> 'deljornada'
             ],
                
             'columns'=> [
                           
                 [
                 'name' => 'id_sys_rrhh_empleados_horario',
                 'type' => TabularColumn::TYPE_HIDDEN_INPUT

                 ],
                 
                 [
                     'name' => 'id_sys_rrhh_horario',
                     'title' => $horarios[0]->getAttributeLabel('id_sys_rrhh_horario'),
                     'type'  => MultipleInputColumn::TYPE_DROPDOWN,
                     'enableError' => true,
                     'items' => ArrayHelper::map(SysRrhhHorarioCab::find()->all(), 'id_sys_rrhh_horario_cab', 'horario'),
                     'options'=>['required'=> true, 'class'=> 'input-sm', 'disabled' => $disabled],
                     'headerOptions'=>[
                         'style'=>'width:20%',
                     ]
                            
                 ],
                         
             ]
         ])?>
      </div>
  </div>
</div>