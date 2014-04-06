<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content">

<p>
	<h1>Revisões do Proprietário <?php echo $dadosProprietario->landholder_name;?></h1>
</p>
<br/>
<p>
Dados atuais do proprietário:<br/><br/>

Nome: <?php echo $dadosProprietario->landholder_name; ?><br/>
Família: <?php echo $dadosProprietario->landholder_family; ?><br/>
Origem: <?php echo $dadosProprietario->landholder_origin; ?><br/>

Colônia: <?php echo $dadosProprietario->nome_colonia;?><br/>
Linha: <?php echo $dadosProprietario->nome_linha;?><br/>
Número do Lote: <?php echo $dadosProprietario->numero_lote;?><br/>

Ano de concessão: <?php echo $dadosProprietario->granting;?><br/>
Ano de quitação: <?php echo $dadosProprietario->release;?><br/>
Preço: <?php echo $dadosProprietario->price;?><br/>
Área informada: <?php echo $dadosProprietario->area;?><br/>
Observações: <?php echo nl2br($dadosProprietario->observation);?><br/>

Status: <?php if ($dadosProprietario->active == 1) { echo 'Ativa'; } else { echo 'Inativa'; }?>
</p>
<br/>

<?php

if (count($fontes)) {
	
?>
<p>
<table>
	<tr>
		<th>Fontes</th>
	</tr>

<?php 
	foreach ($fontes as $fonte) {

?>
	<tr>
		<td>
<?php 

			
				echo 'Título: ' . $fonte['titulo'] . '<br/>';
				echo 'Autor: ' . $fonte['autor'] . '<br/>';
				echo 'Editora: ' . $fonte['editora'] . '<br/>';
				echo 'Página: ' . $fonte['pagina'] . '<br/>';
				echo 'Notas: ' . $fonte['observacao'] . '<br/><br/>';
			

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
Não existem fontes associadas a este proprietário.
</p>
<?php 
}

echo Html::anchor('proprietarios/listagem', 'Voltar');

?> 

</div>
