<?php echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<h1>Gerência de Usuários</h1>

<?php echo Html::anchor('usuarios/inserir', 'Cadastrar novo usuário');?>


<?php

if (count($usuarios)) {

	
?>
<table>
	<thead>
		<tr>
			<th>Data de criação</th>
			<th>Último login</th>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Permissão</th>
			<th>Status</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
<?php 

	foreach ($usuarios as $usuario) {	
		
?>
		<tr>
			<td><?php echo $usuario['created_at']?Date::forge($usuario['created_at'])->format("%d/%m/%Y %H:%M"):''; ?></td>
			<td><?php echo $usuario['last_login']?Date::forge($usuario['last_login'])->format("%d/%m/%Y %H:%M"):''; ?></td>
			<td><?php echo $usuario['profile_fields']['nome']; ?></td>
			<td><?php echo $usuario['email']; ?></td>
			<td><?php echo ($usuario['group']==Model_User::PERMISSAO_ADMINISTRADOR?'Administrador':'Editor'); ?></td>
			<td><?php echo ($usuario['active']==1?'Ativo':'Inativo'); ?></td>
			
			<td>
			<?php echo Html::anchor('usuarios/editar?id=' . $usuario['id'], 'Editar');?> 
			<?php 
			if ($usuario['id'] != $idUsuarioAtual) {
				echo Html::anchor('usuarios/toggle_status?id=' . $usuario['id'], ($usuario['active']==1?'Desativar':'Reativar'), array('onclick' => 'return confirm(\'Tem certeza que deseja '.($usuario['active']==1?'desativar':'reativar').' este usuário?\')'));
			}
			?>
			</td>
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