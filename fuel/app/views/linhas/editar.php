<?php echo $menu; ?>

<div id="content"  style="overflow-y:scroll;">

<script>

//botão para cadastrar fonte
var numeroDeFontes = 1;

function adicionaFonte(id, titulo, autor, editora) {

	if (titulo == 'nop') return false;

	if (id == undefined) id = '';
	if (titulo == undefined) titulo = '';
	if (autor == undefined) autor = '';
	if (editora == undefined) editora = '';

	$("#fontes").append( '<div id="fonte'+numeroDeFontes+'"><p>Informações da fonte: <a href="#" onclick="return removeFonte('+numeroDeFontes+')">Remover</a></p><input type="hidden" name="fonte_id[]" value="'+id+'"><p><label>Título:</label><input type="text" name="fonte_titulo[]" required="required" value="'+titulo+'" '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Autor:</label><input type="text" name="fonte_autor[]" required="required" value="'+autor+'"  '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Editora:</label><input type="text" name="fonte_editora[]" required="required" value="'+editora+'"  '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Página:</label><input type="text" name="fonte_pagina[]" required="required" /></p><p><label>Notas:</label><textarea rows="3" cols="50" required="required" name="fonte_notas[]"></textarea></p></div>');
	
	numeroDeFontes++;
	
}

function removeFonte(numero) {
	$("#fonte"+numero).remove();

	return false;
}

	$(function() {
	
		$("#cadastrar-fonte").click(function() {
			adicionaFonte();
		});

	
		                   $( "#search-fonte-name" ).autocomplete({
		                	   minLength: 2,
		                	   source: "../api/fontes/search",
		                	   select: function( event, ui ) {

		                	        // adiciona a fonte
		                	        adicionaFonte(ui.item.id, ui.item.titulo, ui.item.autor, ui.item.editora);
		                	 
		                	        return false;
		                	      }
		                   }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {

								  if (item.titulo == 'nop') {
									  return $( "<li>" )
			                	        .append( "<a>Nenhuma fonte encontrada</a>")
			                	        .appendTo( ul );
								  } else {
			                   
		                	      	return $( "<li>" )
		                	        .append( "<a><span style='font-weight: bold'>Título:</span> " + item.titulo + "<br><span style='font-weight: bold'>Autor:</span> " + item.autor + "<br><span style='font-weight: bold'>Editora:</span> " + item.editora + "</a>")
		                	        .appendTo( ul );
								  }

		                	        
		                	    };
	});
</script>

<p>Editar Linha</p>
<?php 

echo Form::open('linhas/editar');

?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<p>
	<label for="form_name">Nome:</label>
	<input type="text" required="required" id="form_name" name="nome" value="<?php echo $nome; ?>"/>
</p>
 
<p>
	<label>Colônia</label>
	<select name="colonia" >
	<?php 
	foreach ($colonias as $colonia) {
		echo '<option value="'.$colonia['id'].'" '.($colonia['id']==$id_colonia?'selected="selected"':'').' >'.$colonia['name'] . ($colonia['public']=='1'?'':' [privada]').'</option>' . "\n";
	}
	?>
	</select>
</p>

<p>
Fontes<br>			
								<label for="search-fonte-name">Buscar fonte</label>
								<input id="search-fonte-name" name="fonte_nome" type="text" />ou <a href="#" id="cadastrar-fonte">Cadastrar nova fonte</a>
</p>

<div id="fontes"></div>

<p>
<input type="checkbox" name="notificacao" id="notificacao"/><label for="notificacao">Desejo receber notificações desta linha</label>
</p>

<p>
	<input type="submit" value="Editar" />
</p>
 <?php
 echo Form::close();

echo Html::anchor('linhas/listagem', 'Voltar');
?>

</div>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>