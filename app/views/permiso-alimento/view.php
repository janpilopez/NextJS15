<?php

use app\models\SysRrhhEmpleados;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisoAlimentos */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Permiso Alimentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$nombres = '';
if(!$model->isNewRecord):
    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();
endif;
?>
<div class="sys-rrhh-permiso-alimentos-view">
    <h1><?= "Permiso Alimento No : ".$model->id?></h1>
   <div class = 'panel panel-default'>
     <div class = 'panel-body'>
        <?php $form = ActiveForm::begin(); ?>
    	 <div class ="row">
            <div class="col-md-3">
               <?= $form->field($model, 'id_sys_rrhh_cedula')->textInput(['readonly' => true])?>
            </div>
            <div class="col-md-9">
              <?php echo html::label('Nombres')?>
              <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'readonly'=> true])?>
            
          	</div>
          </div> 
          <div class="row">
          
                <div class="col-md-6">
                <?= $form->field($model, 'inicio')->widget(DatePicker::classname(), [
                                                    'removeButton' => false,
                                                    'size'=>'md',
                                                  
                                                    'pluginOptions' => [
                                                        'autoclose'=>true,
                                                        'format' => 'yyyy-mm-dd',
                                                        'todayHighlight' => true, 
                                                      
                                                    ],
                    				                'options' => ['placeholder' => 'Fecha de Inicio', 'readonly'=> true]
                                                ]);?>
            
           </div>
           <div class="col-md-6">
                  <?= $form->field($model, 'fin')->widget(DatePicker::classname(), [
                                                    'removeButton' => false,
                                                    'size'=>'md',
                                                  
                                                    'pluginOptions' => [
                                                        'autoclose'=>true,
                                                        'format' => 'yyyy-mm-dd',
                                                        'todayHighlight' => true, 
                                                      
                                                    ],
                      'options' => ['placeholder' => 'Fecha de Inicio', 'readonly'=> true]
                                                ]);?>
           </div>   
          
          </div>
          <div class="row">
            <div class="col-md-12">
             <?= $form->field($model, 'motivo')->textarea(['maxlength' => true, 'rows' => 10, 'readonly'=> true])?>
            </div>
    	 </div>
    	 <div class="form-group text-center">
       		<?php if( $model->estado == 'P') :?>
       			 <?= Html::a('Aprobar Permiso', ['aprobar',  'id'=>$model->id], ['class' => 'btn btn-success']) ?>
       	     <?php endif;?>
   	     </div>
        <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
