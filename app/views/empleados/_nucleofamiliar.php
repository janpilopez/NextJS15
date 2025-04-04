<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use unclead\multipleinput\TabularColumn;
use unclead\multipleinput\TabularInput;
use unclead\multipleinput\MultipleInput;
use app\models\SysRrhhEmpleadosNucleoFamiliar;


$con = 0;
if($update != 0):

$con = SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=>$model->id_sys_empresa])->count();

endif;
?>

<div class='row'> 
  <div class= 'col-md-12'>
    <div style="height: 300px; overflow: auto; font-size:11px;">
    <?php $form = ActiveForm::begin([ 'id'=>'formempleados']); ?>
    <div class="row">
        <div class= 'col-md-4'>
            <?= $form->field($model, 'enfermedad')->dropDownList(['N'=> 'NO', 'S'=> 'SI'], ['maxlenght'=> true, 'class'=> 'form-control input-sm','disabled' => $disabled]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
         <?= Html::hiddenInput('datanucleofamiliar', $con, ['id'=> 'datanucleofamiliar'])?>
         
         <?php 
            echo  TabularInput::widget([
             
             'models' => $nucleofamiliar,
             'id'=> 'nucleofamiliar',
             'attributeOptions' => [
              // 'enableAjaxValidation'      => true,
                 /*enableClientValidation'    => false,
                 'validateOnChange'          => false,
                 'validateOnSubmit'          => true,
                 'validateOnBlur'            => false,*/
             ],
             
             'allowEmptyList' => true,
             //'addButtonPosition' => MultipleInput::POS_HEADER,
             'addButtonOptions' =>  [
                 'class' => 'btn btn-xs btn-info',
                 'label' => '<i class="glyphicon glyphicon-plus"></i>'
             ],
             'removeButtonOptions' => [
                 'class' => 'btn btn-xs btn-danger',
                 'label' => '<i class="glyphicon glyphicon-remove"></i>',
              
             ],
                
                
                
             'columns'=> [
                 
                     [
                         'name' => 'id_sys_rrhh_empleados_fam_cod',
                         'type' => TabularColumn::TYPE_HIDDEN_INPUT
                     ],
                     
                     [
                     'name' => 'nombres',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('nombres'),
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..','class'=> 'input-sm', 'disabled' => $disabled
                
                         ],
                     ],
                     [
                         'name' => 'parentesco',
                         'title' => $nucleofamiliar[0]->getAttributeLabel('parentesco'),
                         'type'  => 'dropDownList',
                         'items' => [
                                'C'=>'Conyuge',
                                'H'=>'Hijo',
                                'P'=>'Padres',
                         ],
                         'enableError' => true,
                         'options' => [
                             
                             'placeholder' => 'Seleccione..', 'required'=> true,'class'=> 'input-sm', 'disabled' => $disabled
                             
                         ],
                         'headerOptions'=>[
                             'style'=>'width:10%',
                         ],
                     ],
                 [
                     'name' => 'utilidad',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('utilidad'),
                     'type'  => 'dropDownList',
                     'items' => [
                         'N'=>'NO',
                         'S'=>'SI',
                     ],
                     'enableError' => true,
                     'options' => [
                         'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm', 'disabled' => $disabled
                     ],
                     'headerOptions'=>[
                         'style'=>'width:8%',
                     ],
                 ],
                 [
                    'name' => 'rentas',
                    'title' => $nucleofamiliar[0]->getAttributeLabel('rentas'),
                    'type'  => 'dropDownList',
                    'items' => [
                        0 =>'NO',
                        1 =>'SI',
                    ],
                    'enableError' => true,
                    'options' => [
                        'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm', 'disabled' => $disabled
                    ],
                    'headerOptions'=>[
                        'style'=>'width:8%',
                    ],
                ],
                 [
                     'name' => 'tribunal',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('tribunal'),
                     'type'  => 'dropDownList',
                     'items' => [
                         0 =>'NO',
                         1 =>'SI',
                     ],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'required'=> true,'class'=> 'input-sm', 'disabled' => $disabled
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:8%',
                     ],
                 ],
                 [
                     'name' => 'profesion',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('profesion'),
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'class'=> 'input-sm', 'disabled' => $disabled
                       
                         
                     ],
                 ],
                 [
                     'name' => 'fecha_nacimiento',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('fecha_nacimiento'),
                     'type' => DatePicker::classname(),
                    
                     'enableError' => true,
                     'options' => [
                         'dateFormat' => 'yyyy-MM-dd',
                         'clientOptions' => [
                             'yearRange' => '-115:+0',
                             'changeYear' => true],
                         
                         'options'=> ['class'=> 'form-control input-sm','onblur' => 'calcula.call(this,event)', 'type' => 'date', 'disabled' => $disabled],
                     
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:7%;',
                     ],
                 ],
                 [
                     'name' => 'edad',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('edad'),
                     'type' => TabularColumn::TYPE_TEXT_INPUT,
                     'enableError' => true,
                     'options' => [
                         'class'=> 'input-sm', 'readonly'=> true,
                         'disabled' => $disabled
                
                     ],
                     'headerOptions'=>[
                         'style'=>'width:5%',
                     ],
                     
                 ],
                 [
                     'name' => 'discapacidad',
                     'title' => $nucleofamiliar[0]->getAttributeLabel('discapcidad'),
                     'type'  => 'dropDownList',
                     'items' => [
                         'N'=>'NO',
                         'S'=>'SI',
                     ],
                     'enableError' => true,
                     'options' => [
                         
                         'placeholder' => 'Seleccione..', 'required'=> true, 'class'=> 'input-sm', 'disabled' => $disabled
                         
                     ],
                     'headerOptions'=>[
                         'style'=>'width:3%',
                     ],
                 ],
                     
                
              
             ]
         ])?>
      </div>
  </div>
</div>