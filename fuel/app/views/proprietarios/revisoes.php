<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<p>
	<h1>Revisões do Proprietário <?php echo $dados->landholder_name;?></h1>
</p>
<br/>
<p>
Dados atuais do proprietário:<br/><br/>

Nome: <?php echo $dados->landholder_name; ?><br/>
Família: <?php echo $dados->landholder_family; ?><br/>
Origem: <?php echo $dados->landholder_origin; ?><br/>

Colônia: <?php echo $dados->nome_colonia;?><br/>
Linha: <?php echo $dados->nome_linha;?><br/>
Número do Lote: <?php echo $dados->numero_lote;?><br/>

Ano de concessão: <?php echo $dados->granting;?><br/>
Ano de quitação: <?php echo $dados->release;?><br/>
Preço: <?php echo $dados->price;?><br/>
Área informada: <?php echo $dados->area;?><br/>
Observações: <?php echo nl2br($dados->observation);?><br/>

Status: <?php if ($dados->active == 1) { echo 'Ativa'; } else { echo 'Inativa'; }?>
</p>
<br/>

<?php

if (count($revisoes)) {
	
?>
<p>
<table>
	<tr>
		<th>Código</th>
		<th>Data e Hora</th>
		<th>Usuário</th>
		<th>Status</th>
		<th>Alterações</th>
		<th>Fontes</th>
		<th>Ações</th>
	</tr>

<?php 
	$contadorRevisao = 1;
	foreach ($revisoes as $revisao) {

		

?>
	<tr>
		<td>#<?php echo $revisao->id; ?></td>
		<td><?php echo $revisao->data; ?></td>
		<td><?php echo $revisao->usuario; ?></td>
		<td><?php 
		
		
		if ($revisao->approved == Model_Revision::REVISAO_APROVADA) {
			echo 'Aprovada';
		} else {
			echo 'Reprovada em ' . (!is_null($revisao->data_reprovacao)?Date::forge($revisao->data_reprovacao)->format("%d/%m/%Y %H:%M"):'-') . ' por ' . $revisao->usuario_reprovador;
		} 
		
		?></td>
		<td>
			<ul>
<?php 
		foreach ($revisao->alteracoes as $alteracao) {

			$podeReprovar = true;
			if ($alteracao['old_value'] == '') {
				// primeira revisão, que na verdade é a inclusão da própria colônia
				$podeReprovar = false;
			}
			
			$valorAntigo = $alteracao['old_value'];
			$valorNovo = $alteracao['new_value'];

			switch ($alteracao['attribute']) {
				case 'landholder_name':
					$nomeAtributo = 'Nome do proprietário';
					break;
				case 'landholder_family':
					$nomeAtributo = 'Família';
					break;
				case 'landholder_origin':
					$nomeAtributo = 'Origem';
					break;
				case 'granting':
					$nomeAtributo = 'Ano de concessão';
					break;
				case 'release':
					$nomeAtributo = 'Ano de quitação';
					break;
				case 'observation':
					$nomeAtributo = 'Observações';
					$valorAntigo = nl2br($valorAntigo);
					$valorNovo = nl2br($valorNovo);
					break;
				case 'area':
					$nomeAtributo = 'Área informada';
					break;
				case 'price':
					$nomeAtributo = 'Preço';
					break;
				case 'plot_id':
					$nomeAtributo = 'Lote';
					$valorAntigo = '(Colônia:' . $alteracao['colonia_antiga'] . ', Linha: ' . $alteracao['linha_antiga'] . ', Lote: ' . $alteracao['lote_antigo'] . ')';
					$valorNovo = '(Colônia:' . $alteracao['colonia_nova'] . ', Linha: ' . $alteracao['linha_nova'] . ', Lote: ' . $alteracao['lote_novo'] . ')';
					break;
				case 'active':
					$nomeAtributo = 'Status';
					$valorAntigo = $alteracao['old_value']==1?'Ativo':'Inativo';
					$valorNovo = $alteracao['new_value']==1?'Ativo':'Inativo';
					break;
			}
			
			if ($podeReprovar) {
				$frase = $nomeAtributo . ', de "' . $valorAntigo . '" para "' . $valorNovo . '"';
			} else {
				if ($alteracao['attribute'] == 'fonte') {
					$frase = 'Inclusão de fonte';
				} else {
					$frase = $nomeAtributo . ': ' . $valorNovo;
				}
			}

?>		
				<li><?php echo $frase; ?></li>
<?php 
		}
		
?>
			</ul>
		</td>
		<td>
<?php 
		if (count($revisao->fontes)) {
			foreach ($revisao->fontes as $fonte) {
				echo 'Título: ' . $fonte['titulo'] . '<br/>';
				echo 'Autor: ' . $fonte['autor'] . '<br/>';
				echo 'Editora: ' . $fonte['editora'] . '<br/>';
				echo 'Página: ' . $fonte['pagina'] . '<br/>';
				echo 'Notas: ' . $fonte['observacao'] . '<br/><br/>';
			}
		} else {
			echo "Nenhuma fonte associada.";
		}
?>		
		</td>
		<td>
		<?php 
		if ($revisao->approved == Model_Revision::REVISAO_APROVADA && $contadorRevisao == 1 && $podeReprovar) {
			echo Html::anchor('proprietarios/reprovar_revisao?id=' . $revisao->id, 'Reprovar');
			$contadorRevisao++;
		}
		
		?> 
		</td>
	</tr>
<?php 
	}
	
?>
</table>
</p>
<?php 
	
} else {

?>

<p>
Não existem revisões para este lote.
</p>
<?php 
}

echo Html::anchor('proprietarios/listagem', 'Voltar');

?> 

</div>
