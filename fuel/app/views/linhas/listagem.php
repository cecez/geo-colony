<?php use Fuel\Core\Input;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<h1>Gerência de Linhas</h1>

<?php echo Html::anchor('linhas/inserir', 'Cadastrar nova Linha');?>

<fieldset>
	<form action="" method="post">
	Nome da linha: <input type="text" name="nome_linha" value="<?php echo Input::post('nome_linha'); ?>" /><br/>
	Nome da colônia: <input type="text" name="nome_colonia" value="<?php echo Input::post('nome_colonia'); ?>" /> <input type="submit" value="Buscar"/>
	</form>

</fieldset>

<?php

if (count($linhas)) {

	echo 'Total de ' . count($linhas) . ' resultados';
	
?>
<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Colônia</th>
			<th>Status</th>
			<th>Usuário Criador</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
<?php 

	foreach ($linhas as $linha) {
?>
		<tr>
			<td><?php echo $linha['name']; ?></td>
			<td><?php echo $linha['nome_colonia']; ?></td>
			<td><?php echo ($linha['active']==1?'Ativa':'Inativa'); ?></td>
			<td><?php echo $linha['username']; ?></td>
			<td>
			<?php echo Html::anchor('linhas/visualizar?id=' . $linha['id'], 'Visualizar');?> 
			<?php echo Html::anchor('linhas/editar?id=' . $linha['id'], 'Editar');?> 
			<?php 
			
			if ($linha['number_of_revisions'] > 0) {
				echo Html::anchor('linhas/revisoes?id=' . $linha['id'], 'Revisões (' .$linha['number_of_revisions']. ')');
			} else {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			?> 
			<?php echo Html::anchor('linhas/mudar_status?id=' . $linha['id'], ($linha['active']==1?'Desativar':'Reativar'), array('onclick' => 'return confirm(\'Tem certeza que deseja '.($linha['active']==1?'desativar':'reativar').' esta linha?\')'));?>
			| <?php echo Html::anchor('linhas/notificacoes/' . $linha['id'] . '/' . (!empty($linha['notificacao'])?'desativar':'reativar'), (!empty($linha['notificacao'])?'Desativar notificações':'Receber notificações')); ?></td>
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