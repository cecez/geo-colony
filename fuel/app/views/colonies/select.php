<?php echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<h1>Gerência de Colônias</h1>

<?php echo Html::anchor('colonies/insert', 'Cadastrar nova Colônia');?>


<?php

if (count($colonies)) {
	
?>
<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Tipo</th>
			<th>Status</th>
			<th>Usuário Criador</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
<?php 

	foreach ($colonies as $colony) {
?>
		<tr>
			<td><?php echo $colony['name']; ?></td>
			<td><?php echo 'Colônia ' . ($colony['public']==1?'Pública':'Privada'); ?></td>
			<td><?php echo ($colony['active']==1?'Ativa':'Inativa'); ?></td>
			<td><?php echo $colony['username']; ?></td>
			<td>
			<?php echo Html::anchor('colonies/visualizar?id=' . $colony['id'], 'Visualizar');?> 
			<?php echo Html::anchor('colonies/edit?id=' . $colony['id'], 'Editar');?> 
			<?php 
			
			if ($colony['number_of_revisions'] > 0) {
				echo Html::anchor('colonies/revisions?id=' . $colony['id'], 'Revisões (' .$colony['number_of_revisions']. ')');
			} else {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			?> 
			<?php echo Html::anchor('colonies/toggle_status?id=' . $colony['id'], ($colony['active']==1?'Desativar':'Reativar'), array('onclick' => 'return confirm(\'Tem certeza que deseja '.($colony['active']==1?'desativar':'reativar').' esta colônia?\')'));?>
			| <?php echo Html::anchor('colonies/notificacoes/' . $colony['id'] . '/' . (!empty($colony['notificacao'])?'desativar':'reativar'), (!empty($colony['notificacao'])?'Desativar notificações':'Receber notificações')); ?></td>
		</tr>
<?php 
	}
	
?>
	</tbody>
</table>

<?php 
	
}

?>
</div>