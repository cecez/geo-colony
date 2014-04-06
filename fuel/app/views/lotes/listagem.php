<?php use Fuel\Core\Input;
use Fuel\Core\Pagination;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<h1>Gerência de Lotes</h1>

<?php 

if ($mensagemDeErro) { echo '<p style="color:red">' . $mensagemDeErro . '</p>'; } 

echo Html::anchor('lotes/inserir', 'Cadastrar novo Lote');

?>

<fieldset>
	<form action="<?php echo \Uri::base(false) . 'lotes/listagem'; ?>" method="post">
	Número do lote: <input type="text" name="numero_lote" value="<?php echo Session::get('form_lotes_numero_lote') ?>" /><br/>
	Nome da linha: <input type="text" name="nome_linha" value="<?php echo Session::get('form_lotes_nome_linha') ?>" /><br/>
	Nome da colônia: <input type="text" name="nome_colonia" value="<?php echo Session::get('form_lotes_nome_colonia'); ?>" /> <input type="submit" value="Buscar"/>
	</form>

</fieldset>

<?php

if (count($lotes)) {

	echo 'Total de ' . Pagination::instance('lotes')->total_items . ' resultados. Página <strong>' . Pagination::instance('lotes')->current_page . '</strong> de ' . Pagination::instance('lotes')->total_pages;
	
	echo Pagination::instance('lotes')->render();
	
?>
<table>
	<thead>
		<tr>
			<th>Número</th>
			<th>Linha</th>
			<th>Colônia</th>
			<th>Status</th>
			<th>Usuário Criador</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
<?php 

	foreach ($lotes as $lote) {
?>
		<tr>
			<td><?php echo $lote['number']; ?></td>
			<td><?php echo $lote['nome_linha']; ?></td>
			<td><?php echo $lote['nome_colonia']; ?></td>
			<td><?php echo ($lote['active']==1?'Ativo':'Inativo'); ?></td>
			<td><?php echo $lote['username']; ?></td>
			<td>
			<?php echo Html::anchor('lotes/visualizar?id=' . $lote['id'], 'Visualizar');?> 
			<?php echo Html::anchor('lotes/editar?id=' . $lote['id'], 'Editar');?> 
			<?php 
			
			if ($lote['number_of_revisions'] > 0) {
				echo Html::anchor('lotes/revisoes?id=' . $lote['id'], 'Revisões (' .$lote['number_of_revisions']. ')');
			} else {
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			?> 
			<?php echo Html::anchor('lotes/mudar_status?id=' . $lote['id'], ($lote['active']==1?'Desativar':'Reativar'), array('onclick' => 'return confirm(\'Tem certeza que deseja '.($lote['active']==1?'desativar':'reativar').' este lote?\')'));?>
			| <?php echo Html::anchor('lotes/notificacoes/' . $lote['id'] . '/' . (!empty($lote['notificacao'])?'desativar':'reativar'), (!empty($lote['notificacao'])?'Desativar notificações':'Receber notificações')); ?></td>
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