<table  class="table table-bordered table-condensed" style="<?= $style?>">
    <thead>
      <tr style="background-color: #ccc">
        <th>No</th>
        <th>Area</th>
        <th>Departamento</th>
        <th>C.c</th>
        <th>Nombres</th>
        <th>Tipo Consulta</th>
        <th># Consulta</th>
        <th>Tipo</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Diagnóstico</th>
      </tr>
    </thead>
    <tbody>
     <?php 
      foreach ($datos as $index => $data):
     ?>
     <tr>
     	<td><?= $index + 1?></td>
      	<td><?= $data['area']?></td>
      	<td><?= $data['departamento']?></td>
      	<td><?= $data['id_sys_rrhh_cedula']?></td>
      	<td><?= $data['nombres']?></td>
      	<td><?= $data['recurrencia']?></td>
      	<td><?= $data['numero']?></td>
      	<td><?= $data['tipo']?></td>
      	<td><?= $data['fecha_consulta'] == null ? 'S/D': date('Y-m-d', strtotime($data['fecha_consulta']))?></td>
      	<td><?= $data['fecha_consulta'] == null ? '00:00:00': date('H:i:s', strtotime($data['fecha_consulta']))?></td>
      	<td><?= $data['patologia']?></td>
      </tr>
     <?php endforeach;?>
    </tbody>
 </table>