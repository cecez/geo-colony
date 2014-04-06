<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content">

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
Não existem fontes associadas a esta colônia.
</p>
<?php 
}

echo Html::anchor('colonies/select', 'Voltar');

?> 

</div>
