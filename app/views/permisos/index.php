<?php

use app\models\SysAdmAreas;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmUsuariosDep;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhPermisos;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysRrhhEmpleadosPermisosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Permisos Empleados';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 

$userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();
$areas = [];
$departamentos =[];


if($userdeparta):

    if(trim($userdeparta->area) != ''):
        $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->asArray()->column();
    else:
        $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
    endif;
    
    if(trim($userdeparta->departamento) != ''):
        $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->asArray()->column();
    else:
        $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
    endif;

endif;

$per = [];

$periodosSolicitud = SysRrhhEmpleadosPermisos::find()->select('id_sys_rrhh_permiso')->groupBy('id_sys_rrhh_permiso')->all();

foreach($periodosSolicitud as $periodos){
    array_push($per,$periodos->id_sys_rrhh_permiso);
}


?>
<div class="sys-rrhh-empleados-permisos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Permiso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id_sys_rrhh_empleados_permiso',
           
            [
                'label'=>'Area',
                'attribute'=>'area',
                'filter'=>  ArrayHelper::map(SysAdmAreas::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_area'=> $areas])->all(), 'id_sys_adm_area', 'area'),
                'format' => 'raw',
                'value'=> function($model){
                
                        $cargo        = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->sysRrhhEmpleados->id_sys_adm_cargo])->one();
                        $departamento = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        $area         = SysAdmAreas::find()->where(['id_sys_empresa' => '001'])->andWhere(['id_sys_adm_area'=> $departamento->id_sys_adm_area])->one();
                        
                        if($area):
                            return $area->area;
                        else:
                            return "s/n";
                        endif;
                
                }
             ],
            
             [
                 'label'=>'Departamento',
                 'attribute'=>'departamento',
                 'filter'=>  ArrayHelper::map(SysAdmDepartamentos::find()->where(['estado'=>'A'])->andWhere(['id_sys_adm_departamento'=> $departamentos])->all(), 'id_sys_adm_departamento', 'departamento'),
                 'format' => 'raw',
                 'value'=> function($model){
                 
                         $cargo            = SysAdmCargos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_cargo'=> $model->sysRrhhEmpleados->id_sys_adm_cargo])->one();
                         $departamento     = SysAdmDepartamentos::find()->where(['id_sys_empresa'=> '001'])->andWhere(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
                        
                         if($departamento):
                         
                            return $departamento->departamento;
                         else:
                             return "s/n";
                         endif;
                 }
                 
                 ],
             
             
             
            'id_sys_rrhh_cedula',
            
            [
                'label'=>'Nombres',
                'attribute'=>'nombres',
                'value'=> function($model){
                     return   $model->sysRrhhEmpleados->nombres;
                
                } ,
                'contentOptions'=>['style'=>'width: 5%;']
                ],
            [
            'label'=>'Permiso',
            'attribute'=>'id_sys_rrhh_permiso',
                'filter'=> ArrayHelper::map(SysRrhhPermisos::find()->where(['id_sys_rrhh_permiso'=>$per])->all(), 'id_sys_rrhh_permiso', 'permiso'),
            'value'=> function($model){
            return ($model->sysRrhhPermisos->permiso);
            } ],
            //'fecha_ini',
            [
                'attribute' => 'fecha_ini',
                'filter' => DatePicker::widget([
                   
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_ini',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>['class'=>'form-control input-sm']
                ]),
                   
                'format' => 'raw',
               
            ],
            [
                'attribute' => 'fecha_fin',
                'filter' => DatePicker::widget([
                    'language' => 'es',
                    'model' => $searchModel,
                    'attribute' => 'fecha_fin',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>['class'=>'form-control input-sm']
                ]
                    ),
                'format' => 'raw',
                
            ],
           // 'fecha_fin',
            //'id_sys_empresa',
            //'transaccion_usuario',
            //'estado',
            //'comentario',
           [ 
                'label'=>'Jornada',
                'attribute'=>'tipo',
                'filter'=> ['C' => 'Completa','P' => 'Parcial','D' => 'Completa-Laboral', 'O'=> 'Parcial-Laboral'],
                'value'=> function($model){
                    if($model->tipo == 'C'){
                        return 'Completa';
                    }elseif($model->tipo == 'P'){
                        return 'Parcial';
                    }elseif($model->tipo == 'D'){
                        return 'Completa-Laboral';
                    }else{
                        return 'Parcial-Laboral';
                    }
                    
                } 
            ],
            
            [
                //'header' => 'Estado',
                'attribute'=> 'estado',
                'filter'=> ['A' => 'Aprobado','P' => 'En Proceso', 'No Aprobado'],
                'value' => function ($model) {
                if ($model->Estadosolicitud == 'P') {
                    return Html::a('En Proceso', [''], [
                        'class' => 'btn btn-xs btn-warning btn-block',
                    ]);
                }elseif($model->Estadosolicitud == 'A'){
                    return Html::a('Aprobado',[''], [
                        'class' => 'btn btn-xs btn-success btn-block',
                    ]);
                }else{
                    return Html::a('No Aprobado',[''], [
                        'class' => 'btn btn-xs btn-danger btn-block',
                    ]);
                }
                },
                'format' => 'raw',
                
                ],
            
            //'hora_ini',
            //'hora_fin',

                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'<div class="text-center" style="display:flex"> {view}&nbsp{update}&nbsp{anular}&nbsp{delete}&nbsp{aprobar}',
                    'buttons'=>[
                        
                        'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Ver Permiso',
                            
                        ]);
                        },
                        'update' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => 'Actualizar',
                            
                        ]);
                        },

                        'anular' => function($url, $model){
                        
                            $usuariotipo = SysAdmUsuariosDep::find()->select('usuario_tipo')->where(['id_usuario'=> Yii::$app->user->id])->one();
                            $ids = ArrayHelper::getValue($usuariotipo, 'usuario_tipo');
                        
                            if( $ids == 'G' ){
                                return Html::a('<span class="glyphicon glyphicon-ban-circle"></span>', '#', [
                                    'id' => 'abrir-ventana',
                                    'title' => 'Rechazar Permiso',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal',
                                    'data-url' => Url::to(['anular', 'id'=>$model->id_sys_rrhh_empleados_permiso]),
                                    'data-pjax' => '0',                                
                                ]);
                            }
                        
                        },
                        
                        
                        'delete' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => 'Anular Permiso',
                            'data' => [
                                'confirm' => 'Está seguro que desea anular el siguiente registro?',
                                'method' => 'post',
                            ],
                            
                        ]);
                        },
                        'aprobar' => function($url, $model){
                        
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                            'title' => 'Aprobar Permiso',
                            'data' => [
                                'confirm' => 'Está seguro de aprobar el permiso?',
                                'method' => 'post',
                            ],
                            
                        ]);
                        },
                        
                        
                        ],
                        'urlCreator'=>function($action,$data){
                        if($action=='view'){
                            
                            return ['permisos/view','id'=>$data->id_sys_rrhh_empleados_permiso];
                        }
                        
                        if($action == 'update'){
                            return ['permisos/update','id'=>$data->id_sys_rrhh_empleados_permiso];
                        }

                        if($action =='anular'){
                            
                            return ['permisos/anular','id'=> $data->id_sys_rrhh_empleados_permiso, 'id_sys_empresa'=> $data->id_sys_empresa ];
                        }
                        
                        if($action =='delete'){
                            
                            return ['permisos/delete','id'=> $data->id_sys_rrhh_empleados_permiso, 'id_sys_empresa'=> $data->id_sys_empresa ];
                        }
                        if($action =='aprobar'){
                            
                            return ['permisos/aprobar','id_sys_rrhh_empleados_permiso'=> $data->id_sys_rrhh_empleados_permiso, 'id_sys_empresa'=> $data->id_sys_empresa ];
                        }
                        }
                        ],
            
        ],
    ]); ?>


</div>


<?php
$this->registerJs(
    "$(document).on('click', '#abrir-ventana', (function() {
        $.get(
            $(this).data('url'),
            function (data) {
                $('.modal-body').html(data);
                $('#modal').modal();
            }
        );
    }));"
); ?>
 
<?php
Modal::begin([
    'id' => 'modal',
    'header' => '<h4 class="modal-title">Comentario de Anulación</h4>',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Cerrar</a>',
]);
 
echo "<div class='well'></div>";
 
Modal::end();
?>
