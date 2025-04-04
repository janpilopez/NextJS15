<?php
use app\models\SysRrhhEmpleadosFotoUniformes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\assets\EmpleadosAsset;
use yii\web\JsExpression;
use kartik\typeahead\Typeahead;
use app\models\User;
use app\models\SysRrhhEmpleados;
use yii\jui\DatePicker;

$inputDisable = true;

 if(!User::hasRole('auditExterno')): 

   $inputDisable = false;
 
 endif;

$nombres = '';

if(!$model->isNewRecord):

    $nombres =  SysRrhhEmpleados::find()->select('nombres')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->scalar();

endif;
/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhEmpleados */

$this->title = 'Visualizar Uniforme Empleado';
$this->params['breadcrumbs'][] = ['label' => 'Uniforme Empleado', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_rrhh_cedula, 'url' => ['view', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => $model->id_sys_empresa]];
$this->params['breadcrumbs'][] = 'Visualizar';
?>
<div class="sys-rrhh-empleados-uniforme-view">

<?php $form = ActiveForm::begin(['id'=> 'form-empleadosuniforme']); ?>
    <div class = 'panel panel-default'>  
     <div class = 'panel-body'>
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Datos Empleados</a></li>
        </ul>
        <br>
        <div class="tab-content">
         <div id="home" class="tab-pane fade in active">
            <div class = 'panel-body'>
                    <div class= 'row'>
                    <div class= 'col-md-3'>
                    <?php
                   
                    echo $form->field($model, 'fecha_entrega')->widget(DatePicker::classname(),[
                      'name' => 'fecha_entrega', 
                      'dateFormat' => 'yyyy-MM-dd',
                      'clientOptions' => [
                      'yearRange' => '-115:+0',
                      'changeYear' => true],

                      'options' => ['class'=> 'form-control input-sm', 'id'=> 'fecha_entrega', 'type'=> 'date', 'disabled' => $inputDisable]
                ]);?>
                        </div>
                     <div class='col-md-2'> 
                           <?php  
                           $template = '<div style="font-size:8px;"><div class="repo-language">{{nombres}}</div>' .
                               '<div class="repo-description"><span class="text-muted" ><small>{{value}}</small></span></div></div>';
                           
                           echo $form->field($model, 'id_sys_rrhh_cedula')->widget(Typeahead::classname(), [
                               'options' => ['placeholder' => 'Buscar..',  'class'=> 'form-control input-sm', 'id' => 'id_sys_rrhh_cedula','disabled' => $inputDisable],
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
                                            getPeriodos(suggestion.value, "G");

                                            let tabla       = document.querySelector("#list-periodos > tbody");
                                            tabla.innerHTML = "";
                                        }',
                                     
                                       
                                      /* $('#autocomplete').on('typeahead:cursorchanged', function (e, datum) {
                                           console.log(datum);
                                       })*/
                                   ]
                                   
                                
                           ])->label('Cédula');
                           ?>
                      </div>
                      <div class= 'col-md-3'>
                        <?php echo html::label('Nombres')?>
                        <?php echo html::textInput('nombres', $nombres, ['class'=> 'form-control input-sm', 'id'=> 'nombres', 'disabled'=> true])?>
                      </div>
                        <div class= 'col-md-2'>
                           <?= $form->field($model, 'numero_uniforme')->textInput(['maxlength' => true, 'class'=> 'form-control input-sm',  'placeholder'=> 'Número de Uniforme', 'disabled' => $inputDisable])?>
                        </div>   
                      </div> 
                      <div class = 'panel panel-default'>  
                        <div class = 'panel-body'>   
                            
                        <?php if ($fotos) :?>
                
                        <img width="20%" height ='20%' src="data:image/jpeg;base64, <?= $fotos?>" alt="" />
                        
                        <?php else : ?>
                        
                           <img width="20%" height ='20%' src='<?= Yii::$app->homeUrl.'img/sin_foto.jpg'?>' alt="" />
                              
                        <?php endif ?>
                     </div>
           </div>
                    </div>
            </div>
        </div>
          
        </div>  
        <?php ActiveForm::end(); ?>
</div>