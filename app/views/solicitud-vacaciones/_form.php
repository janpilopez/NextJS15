<?php

use app\models\SysAdmPeriodoVacaciones;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPeriodoVacaciones;
use app\models\User;
use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use app\assets\SolicitudVacacionesAsset;
SolicitudVacacionesAsset::register($this);

$periodos = json_encode( SysAdmPeriodoVacaciones::find()->select('id_sys_adm_periodo_vacaciones, periodo')->where(['id_sys_empresa'=> '001'])->andWhere(['>=','anio_vac_hab', date('Y')])->asArray()->all());

//Adelantar Periodos


//Finalizar peridos

$tipo = json_encode(['G','P','A']);
$url = Yii::$app->urlManager->createUrl(['solicitud-vacaciones']);
$inlineScript = "var url='$url', periodosVaciones = '$periodos', tipo = {$tipo}, update = '$update';";
$this->registerJs($inlineScript, View::POS_HEAD);

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhVacacionesSolicitud */
/* @var $form yii\widgets\ActiveForm */

$nombres = '';

if(!$model->isNewRecord):

    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();

endif;

$ban = true;
//opcion para habilitar vacaciones pagadas solo a ddoo y gerencia 
if(User::hasRole('GERENTE') ||  User::hasRole('jefeDDOO') || User::hasRole('jefeNomina') || User::hasRole('asistNomina')):
  $ban= false;
endif;

?>
<div class="sys-rrhh-vacaciones-solicitud-form">

    <?php $form = ActiveForm::begin(['id'=> 'form-vacaciones']); ?>
   <div class = 'row'>     
    <div class = 'panel panel-default'>
        <div class = 'panel-body'>
            <div class = 'col-md-6'>
             <?php if ($update != 2 ): ?> 
                  <div class = 'row'>
                     <div class='col-md-4'> 
                           <?php  
                           $template = '<div style="font-size:8px;"><div class="repo-language">{{nombres}}</div>' .
                               '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                           
                           echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                               'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm', 'disabled'=> $update != 1  ?false: true, 'id' => 'id_sys_rrhh_cedula'],
                               'pluginOptions' => ['highlight'=>true],
                               'scrollable'=>true,
                               'dataset' => [
                                   [
                                       
                                       'remote' => [
                                           'url' =>    Url::to(['consultas/listempleados2']) . '?q=%QUERY',
                                           'wildcard' => '%QUERY'
                                       ],
                                       'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                       'display' => 'value',
                                       'templates' => [
                                           'notFound' => '<div class="text-danger" style="padding:0 8px;font-size:10px;">No se encuentra</div>',
                                           'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
                                           ],
                                          
                                   ]
                                  
                                   ],'pluginEvents' => [
                                       'typeahead:select' => 'function(ev, suggestion) { 
                                            console.log(suggestion);
                                            $("#nombres").val(suggestion.nombres);
                                            document.getElementById("tipo").value = "G"
                                            ObtenerUltimaFechaCulminacionVacaciones(suggestion.value);
                                            getSolicitudPendiente(suggestion.value);
                                            getPeriodos(suggestion.value, "G");

                                            let tabla       = document.querySelector("#list-periodos > tbody");
                                            tabla.innerHTML = "";
                                        }',
                                     
                                       
                                      /* $('#autocomplete').on('typeahead:cursorchanged', function (e, datum) {
                                           console.log(datum);
                                       })*/
                                   ]
                                   
                                
                           ])->label('Cedula');
                           ?>
                      </div>
                      <div class= 'col-md-8'>
                        <?php echo html::label('Nombres')?>
                        <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
                      </div>
                  </div>
                  <div class= 'row'>
                     <div class = 'col-md-6'>
                        
                          <?= $form->field($model, 'fecha_inicio')->widget(DatePicker::classname(), [
                                                                'removeButton' => false,
                                                                'size'=>'md',
                                                                 //'value'=> date('Y-m-d'),
                                                                'pluginOptions' => [
                                                                    'autoclose'=>true,
                                                                    'format' => 'yyyy-mm-dd',
                                                                    //'startDate' => date('Y-m-d'),
                                                                ],
                              'options' => ['placeholder' => 'Fecha Inicio','id'=>'fechaini', 'onchange' => 'CalculaDias.call(this,event)','disabled'=> $update != 1  ?false: true]
                                   ]);?>
                     </div>
                     <div class ='col-md-6'>
                          <?= $form->field($model, 'fecha_fin')->widget(DatePicker::classname(), [
                                                                'removeButton' => false,
                                                                'size'=>'md',
                                                                 //'value'=> date('Y-m-d'),
                                                                'pluginOptions' => [
                                                                    'autoclose'=>true,
                                                                    'format' => 'yyyy-mm-dd',
                                                                    //'startDate' => date('Y-m-d'),
                                                                ],
                              'options' => ['placeholder' => 'Fecha Fin', 'id'=> 'fechafin', 'onchange' => 'CalculaDias.call(this,event)', 'disabled'=> $update != 1  ?false: true ]
                           ]);?>
                     </div>
                   </div>
                   <div class= 'row'>
                     <div class= 'col-md-5'>
                         <?= $form->field($model, 'id_sys_rrhh_vacaciones_periodo')->dropDownList(ArrayHelper::map(SysAdmPeriodoVacaciones::find()->where(['id_sys_empresa'=> '001'])->andWhere(['<=','anio_vac_hab', date('Y')])->all(), 'id_sys_adm_periodo_vacaciones', 'periodo'), ['class'=> 'form-control', 'prompt'=> 'Seleccionar', 'disabled'=> $update != 1  ?false: true]) ?>
                     </div>
                     <div class ='col-md-4'>
                        <label>Días Gozados</label>
                        <?= html::textInput('dias', '', ['id'=> 'dias', 'class'=>'form-control', 'disabled'=> true])?>
                     </div>
                      <div class= 'col-md-3'>
                        <?= $form->field($model, 'tipo')->dropDownList(['G'=> 'Gozadas', 'P'=> 'Pagadas', 'A'=> 'Anticipadas'], ['disabled'=> $ban, 'id' => 'tipo'])?>
                     </div>
                   </div>
               
               <?php endif;?>
           
               <div class= 'row'>
                 <div class ='col-md-12'>
                    <?= $form->field($model, 'comentario')->textarea(['maxlength' => true, 'row'=> 2, 'disabled'=> $update != 1  ?false: true]) ?>
                 </div>
               </div>
               <div class= 'row'>
               	 <div class= 'col-md-12'>
               	 	<?= Html::hiddenInput('ultima_fecha_culminacion_vacaciones', '1900-01-01',['id' => 'ultima_fecha_culminacion_vacaciones'])?>
               	 </div>
               </div>
               <div class= 'row'>
               	 <div class= 'col-md-12'>
               	 	<?= Html::hiddenInput('dias_solicitud_pendiente',999,['id' => 'dias_solicitud_pendiente'])?>
               	 </div>
               </div>
               <div class= 'row'>
               	 <div class= 'col-md-12'>
               	 	<?= Html::hiddenInput('periodo_anterior',999,['id' => 'periodo_anterior'])?>
               	 </div>
               </div>
               
            </div> 
             
             <?php
               if ($update != 2 ): 
               //oculta campos en caso de anular y solo muestra el comentario
             ?>
             
             
         
                        <div  class ='col-md-6'>
                           <table id='list-periodos' class="table table-bordered table-condensed" style="background-color: white; font-size: 11px; width: 100%">
                            <caption>Periodos Disponibles</caption>
                            <thead>
                              <tr>
                                <th>Periodos</th>
                                <th>Dias Disponibles</th>
                                <th>Dias Otorgados</th>
                                <th>Dias Pendientes</th>
                                <th>Estado</th>

                              </tr>
                            </thead>
                            <tbody>
                               <?php 
                               if(!$model->isNewRecord):
                                   
                                    $datos = (new \yii\db\Query())->select(['a.id_sys_adm_periodo_vacaciones', 'dias_disponibles', 'dias_otorgados', 'estado', 'periodo', '(dias_disponibles - dias_otorgados) as dias_pendientes'])
                                   ->from('sys_rrhh_empleados_periodo_vacaciones a')
                                   ->innerjoin('sys_adm_periodo_vacaciones b', 'a.id_sys_adm_periodo_vacaciones  = b.id_sys_adm_periodo_vacaciones')
                                   ->where("a.id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")
                                   ->andwhere("a.id_sys_empresa= '{$model->id_sys_empresa}'")
                                   //->andWhere("anio_vac_hab < YEAR(GETDATE())")
                                   ->orderby('anio_vac desc')
                                   ->all(SysRrhhEmpleadosPeriodoVacaciones::getDb());
                                   
                                   if(count($datos) > 0):
                                      
                                       foreach ($datos as $data):
                                        ?> 
                                          <tr>
                                             <td><?= html::hiddenInput('dias_pen', $data['dias_pendientes'], ['id'=> $data['id_sys_adm_periodo_vacaciones']]) ?><?= $data['periodo']?></td>
                                             <td><?= $data['dias_disponibles']?></td>
                                             <td><?= $data['dias_otorgados']?></td>
                                             <td><?= $data['dias_pendientes']?></td>
                                              <?php 
                                              if($data['dias_pendientes'] <= 0 ):
                                              ?>
                                                <td bgcolor= <?= $data['dias_pendientes'] < 0 ? '#E78E07': '#85f387' ?>><?= $data['estado']=='A'?'Anticipadas':'Tomadas'?></td>
                                              <?php else:
                                              ?>
                                                <td bgcolor= <?= $data['dias_pendientes'] > 0 ? '#ffeeba': '#85f387' ?>><?= $data['estado']=='P'?'Pendientes':'Tomadas'?></td>
                                              <?php   
                                              endif;
                                              ?>
                                            </tr>
                                        <?php   
                                       endforeach;
                                     endif;
                                 endif;
                               ?>
                            </tbody>
                         </table>
                        </div>    
                  <?php endif;?>
            
         </div>
        <div class="form-group text-center">
            <?= Html::submitButton('Guardar Datos', ['class' => 'btn btn-success', 'id'=> 'btn-guardar', 'disabled'=> $update != 1  ?false: true]) ?>
        </div>
      </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<div id= "loading"></div>
