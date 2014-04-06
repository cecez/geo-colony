<?php use Fuel\Core\Input;
use Fuel\Core\Pagination;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<h1>Gerência de Proprietários</h1>

<?php echo Html::anchor('proprietarios/inserir', 'Cadastrar novo Proprietário');?>

<fieldset>
	<form action="<?php echo \Uri::base(false) . 'proprietarios/listagem'; ?>" method="post">
	Nome, família ou origem do proprietario: <input type="text" name="proprietario" value="<?php echo Session::get('form_proprietarios_proprietario') ?>" /><br/>
	Número do lote: <input type="text" name="numero_lote" value="<?php echo Session::get('form_proprietarios_numero_lote') ?>" /><br/>
	Nome da linha: <input type="text" name="nome_linha" value="<?php echo Session::get('form_proprietarios_nome_linha') ?>" /><br/>
	Nome da colônia: <input type="text" name="nome_colonia" value="<?php echo Session::get('form_proprietarios_nome_colonia'); ?>" /> <input type="submit" value="Buscar"/>
	</form>

</fieldset>

<?php

if (count($proprietarios)) {

	echo 'Total de ' . Pagination::instance('proprietarios')->total_items . ' resultados. Página <strong>' . Pagination::instance('proprietarios')->current_page . '</strong> de ' . Pagination::instance('proprietarios')->total_pages;
	
	echo Pagination::instance('proprietarios')->render();
	
?>
<table>
	<thead>
		<tr>
			<th>Nome do proprietário</th>
			<th>Família do proprietário</th>
			<th>Origem do proprietário</th>
			<th>Número do Lote</th>
			<th>Linha</th>
			<th>Colônia</th>
			<th>Status</th>
			<th>Usuário Criador</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
<?php 

	foreach ($proprietarios as $proprietario) {
?>
		<tr>
			<td><?php echo $proprietario['landholder_name']; ?></td>
			<td><?php echo $proprietario['landholder_family']; ?></td>
			<td><?php echo $proprietario['landholder_origin']; ?></td>
			<td><?php echo $proprietario['numero_lote']; ?></td>
			<td><?php echo $proprietario['nome_linha']; ?></td>
			<td><?php echo $proprietario['nome_colonia']; ?></td>
			<td><?php echo ($proprietario['active']==1?'Ativo':'Inativo'); ?></td>
			<td><?php echo $proprietario['username']; ?></td>
			<td>
			<?php echo Html::anchor('proprietarios/visualizar?id=' . $proprietario['id'], 'Visualizar');?> 
			<?php echo Html::anchor('proprietarios/editar?id=' . $proprietario['id'], 'Editar');?> 
			<?php 
			
			if ($proprietario['number_of_revisions'] > 0) {
				echo Html::anchor('proprietarios/revisoes?id=' . $proprietario['id'], 'Revisões (' .$proprietario['number_of_revisions']. ')');
			} else {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			?> 
			<?php echo Html::anchor('proprietarios/mudar_status?id=' . $proprietario['id'], ($proprietario['active']==1?'Desativar':'Reativar'), array('onclick' => 'return confirm(\'Tem certeza que deseja '.($proprietario['active']==1?'desativar':'reativar').' este proprietário?\')'));?>
			| <?php echo Html::anchor('proprietarios/notificacoes/' . $proprietario['id'] . '/' . (!empty($proprietario['notificacao'])?'desativar':'reativar'), (!empty($proprietario['notificacao'])?'Desativar notificações':'Receber notificações')); ?></td>
		</tr>
<?php 
	}
	
?>
	</tbody>
</table>

<?php 
	
} else {
	echo 'Sem resultados.';
}

?>
</div>