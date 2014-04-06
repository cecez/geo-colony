<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content">

<p>
	<h1>Revisões da Linha <?php echo $dadosLinha->name;?></h1>
</p>
<br/>
<p>
Dados atuais da linha:<br/><br/>

Nome: <?php echo $dadosLinha->name;?><br/>
Colônia: <?php echo $dadosLinha->nome_colonia;?><br/>
Status: <?php if ($dadosLinha->active == 1) { echo 'Ativa'; } else { echo 'Inativa'; }?>
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
Não existem fontes associadas a esta linha.
</p>
<?php 
}

echo Html::anchor('linhas/listagem', 'Voltar');

?> 

</div>
