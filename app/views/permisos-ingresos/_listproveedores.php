
<?php
use yii\helpers\Html;
$holgura =  15;
//listado de funciones de calculos

$meses =  Yii::$app->params['meses'];
$dias =   Yii::$app->params['dias'];

class FilterColumn {
    private $colName;
    
    function __construct($colName) {
        $this->colName = $colName;
    }
    
    function getValues($i) {
        return $i[$this->colName];
    }
}

class FilterData {
    private $colName;
    private $value;
    
    function __construct($colName, $value) {
        $this->colName = $colName;
        $this->value = $value;
    }
    
    function getFilter($i) {
        return $i[$this->colName] == $this->value;
    }
}

?>
<style>
#tablemodal_filter > label{
    width:100%;
}
.table__no-margin{
    margin-bottom: 0px;
}
</style>
<script>
function seleccionTr(tr){
	
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		 
		    if(m.checked){
			 
		    	m.parentElement.parentElement.classList.add("seleccion")
		 	  // tr.classList.remove("seleccion")
		    }else{
			    
		    	m.parentElement.parentElement.classList.remove("seleccion")
			    }
			})
}
$('#tablemodal').DataTable( {
    "scrollY":        "60vh",
    "scrollX":        "100%",
    "scrollCollapse": true,
    "paging":         false,
    "ordering": false,
})

$("#tablemodal > tbody > tr").bind('dblclick', function(){
	seleccionTr(this)
	//selectOrden(this.firstElementChild.innerHTML.trim())
})

$("#tablemodal > tbody > tr").bind('click', function(){
	seleccionTr(this)
})

$("#btnCancelar").on('click', (e)=>{
	e.preventDefault()
	$("#modalproveedores").modal("hide")
})

$("#btnAgregar").on('click', (e)=>{
	e.preventDefault()
	
	 let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	 ant.map((m)=>{
		 
		    if(m.checked){

		    	insertarfila(m.id,m.value);
		    	m.parentElement.parentElement.classList.add("seleccion")
				
		    }
		    
			})
		
	$("#modalproveedores").modal("hide")
})

$(document).ready(function() {
   $("#select").click(function() {
      $(':checkbox').not(this).prop('checked', this.checked);
	  	let ant = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
		ant.map((m)=>{
				
			if(m.checked){
		    	m.parentElement.parentElement.classList.add("seleccion")
		 	  // tr.classList.remove("seleccion")
		    }else{
		    	m.parentElement.parentElement.classList.remove("seleccion")
			}		
		})
   });
});

</script>
<div class="row">
	<div class="col-xs-12">
		<table id="tablemodal" class="table table-bordered table-condensed table__no-margin" style="width:100%; background-color: white; font-size: 11px;">
    		<thead>
				<th colspan= "2" class="text-right">Marcar / Desmarcar</th>
				<th><input type="checkbox" id="select"></th>
                <tr>
                   <th>Cédula</th>
                   <th>Nombres</th>
                   <th>Seleccionar</tr>
                </tr>
        	</thead>
        	<tbody>
        		<?php 
				
					foreach ($datos as $index => $data):
							
				 ?>
        			<tr>
        				<td><?= $data['id_sys_rrhh_cedula'] ?></td>
        				<td><?= $data['nombres'] ?></td>
        				<td style ="text-align:center;"><input type="checkbox" id="<?= $data['id_sys_rrhh_cedula'] ?>" value="<?= $data['nombres'] ?>"></td>
        			</tr>
    
				<?php endforeach; ?>
        	</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center">
		<?= Html::a("Aceptar", "", ['id'=>'btnAgregar','class'=>"btn btn-success", 'style'=>'width:45%']) ?>
		<?= Html::a("Cancelar", "", ['id'=>'btnCancelar','class'=>"btn btn-danger", 'style'=>'width:45%']) ?>
	</div>
</div>
