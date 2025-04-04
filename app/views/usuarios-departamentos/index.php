<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\controllers\EmpresasController;
use app\models\SysEmpresa;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysAdmUsuariosDepSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios Departamentos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-adm-usuarios-dep-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Agregar', ['registrar'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           // 'id_sys_adm_usuarios_dep',
            // 'id_usuario',
           [
               
                'label'=> 'Usuarios',
                'filter'=> ArrayHelper::map(User::find()->where(['empresa'=> $_SESSION['empresa']])->andwhere(['status'=> '1'])->addOrderBy('username')->all(), 'id', 'username'),
                'attribute'=>'id_usuario',
                'format' => 'raw',
                'value'=> function($model){
                         $user =  User::find()->where(['id'=>$model->id_usuario])->one();
                        if($user):
                         return $user->username;
                        endif;
                } ,
            ],
         
            [
                'label'=>'Area',
                'filter'=> ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'),
                'attribute'=>'area',
                //'filter'=> ArrayHelper::map(SysAdmAreas::find()->all(), 'id_sys_adm_area', 'area'), ['class'=>'form-control input-sm', 'id'=>'area', 'prompt' => 'Todos',  'options'=>[ $area => ['selected' => true]]],
                'value'=> function($model){
                       
                if($model->area != null):
                    $area = SysAdmAreas::find()->where(['id_sys_adm_area'=> $model->area])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
                    if($area):
                       return $area->area;
                     else:
                      return 's/n';
                     endif;

                else:
                     return "Todos";
                
                endif;
                
                } ,
            ],
            [
                'label'=>'Departamento',
                //'filter'=> ArrayHelper::map(SysAdmDepartamentos::find()->andFilterWhere(['id_sys_adm_area'=> '2'])->all(), 'id_sys_adm_departamento', 'departamento'),
                'attribute'=>'departamento',
                'value'=> function($model){
                    
                if($model->departamento != null):
                
                        $departamento  = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->departamento])->one();
                
                        if($departamento):
                        
                           return $departamento->departamento;
                        else:
                            return 's/n';
                        endif;
                  else:
                       return "Todos";
                 endif;
                  
                
                } ,
                ],
          //  'departamento',
           //  'id_sys_empresa',
               [
                'label'=>'Tipo Usuario',
                'attribute'=>'usuario_tipo',
                'value'=> function($model){
                    $tipos = Yii::$app->params['tipo_usuarios'];
                    return $tipos[$model->usuario_tipo];
                } ,
             ],
             [
                 'label'=>'Estado',
                 'attribute'=>'estado',
                 'value'=> function($model){
                         return $model->estado == 'A' ? 'Activo': 'Inactivo';
                         //SysAdmUsuariosDep
                 } ,
                 ],
             
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
