<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<p>
	<h1>Revisões da Colônia <?php echo $dadosColonia->name;?></h1>
</p>
<br/>
<p>
Dados atuais da colônia:<br/><br/>

Nome: <?php echo $dadosColonia->name;?><br/>
Colônia <?php if ($dadosColonia->public == Controller_Colonies::COLONIA_PUBLICA) { echo 'Pública'; } else { echo 'Privada'; }?><br/>
Status: <?php if ($dadosColonia->active == 1) { echo 'Ativa'; } else { echo 'Inativa'; }?>
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

			switch ($alteracao['attribute']) {
				case 'name':
					$nomeAtributo = 'Nome';
					$valorAntigo = $alteracao['old_value'];
					$valorNovo = $alteracao['new_value'];
					break;
				case 'public':
					$nomeAtributo = 'Tipo de colônia';
					$valorAntigo = $alteracao['old_value']==Controller_Colonies::COLONIA_PUBLICA?'Pública':'Privada';
					$valorNovo = $alteracao['new_value']==Controller_Colonies::COLONIA_PUBLICA?'Pública':'Privada';
					break;
				case 'active':
					$nomeAtributo = 'Status';
					$valorAntigo = $alteracao['old_value']==1?'Ativa':'Inativa';
					$valorNovo = $alteracao['new_value']==1?'Ativa':'Inativa';
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
			echo Html::anchor('colonies/revision_reject?id=' . $revisao->id, 'Reprovar');
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
Não existem revisões para esta colônia.
</p>
<?php 
}

echo Html::anchor('colonies/select', 'Voltar');

?> 

</div>
