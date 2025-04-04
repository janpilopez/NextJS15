
<?php
use yii\helpers\Html;
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
	$("#modal").modal("hide")
})




$("#check").on('change',  function(e){
	e.preventDefault()

	let checkbox  = [...document.querySelectorAll("#tablemodal > tbody > tr input[type='checkbox']")]
	
	if(this.checked){
		
		checkbox.map((m)=>{
			 m.checked = true;
		});
		
	}else{
		
		checkbox.map((m)=>{
			 m.checked = false;
	    });	
	}


	
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
		
	$("#modal").modal("hide")
})
</script>
<div class="row">
	<div class="col-xs-12">
		<table id="tablemodal" class="table table-bordered table-condensed table__no-margin" style="width:100%; background-color: white; font-size: 11px;">
    		<thead>
                <tr>
                   <th>Cédula</th>
                   <th>Nombres</th>
                   <th><?= html::checkbox('chekemp', false, ['id'=> 'check'])?></th>
                </tr>
        	</thead>
        	<tbody>
        		<?php foreach($datos as $index => $dato){ ?>
        			<tr>
        				<td><?= $dato["id_sys_rrhh_cedula"] ?></td>
        				<td><?= $dato["nombres"] ?></td>
        				<td style ="text-align:center;"><input  type="checkbox" id="<?= $dato['id_sys_rrhh_cedula'] ?>" value="<?=$dato['nombres']?>" ></td>
        			</tr>
        		<?php } ?>
        	</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center">
		<?= Html::a("Aceptar", "", ['id'=>'btnAgregar','class'=>"btn btn-success", 'style'=>'width:45%']) ?>
		<?= Html::a("Cancelar", "", ['id'=>'btnCancelar','class'=>"btn btn-danger", 'style'=>'width:45%']) ?>
	</div>
</div>
