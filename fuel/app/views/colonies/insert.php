<?php echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<script type="text/javascript">

//botão para cadastrar fonte
var numeroDeFontes = 1;

function adicionaFonte(id, titulo, autor, editora) {

	if (titulo == 'nop') return false;

	if (id == undefined) id = '';
	if (titulo == undefined) titulo = '';
	if (autor == undefined) autor = '';
	if (editora == undefined) editora = '';

	$("#fontes").append( '<div id="fonte'+numeroDeFontes+'"><p>Informações da fonte: <a href="#" onclick="return removeFonte('+numeroDeFontes+')">Remover</a></p><input type="hidden" name="fonte_id[]" value="'+id+'"><p><label>Título:</label><input type="text" required="required" name="fonte_titulo[]" value="'+titulo+'" '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Autor:</label><input type="text" required="required" name="fonte_autor[]" value="'+autor+'"  '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Editora:</label><input type="text" name="fonte_editora[]" required="required"  value="'+editora+'"  '+(id!=''?'readonly="readonly"':'')+' /></p><p><label>Página:</label><input type="text" required="required" name="fonte_pagina[]" /></p><p><label>Notas:</label><textarea rows="3" cols="50" required="required" name="fonte_notas[]"></textarea></p></div>');
	
	numeroDeFontes++;
	
}

function removeFonte(numero) {
	$("#fonte"+numero).remove();

	return false;
}

	$(function() {
		
		$('#form_tipo_privada').click(function() { 
			$('#p_colaboradores').show();
		});

		$('#form_tipo_publica').click(function() { 
			$('#p_colaboradores').hide();
		});

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
			                   
		                	      	return $( "<li style='border-bottom: 1px solid red;'>" )
		                	        .append( "<a><span style='font-weight: bold'>Título:</span> " + item.titulo + "<br><span style='font-weight: bold'>Autor:</span> " + item.autor + "<br><span style='font-weight: bold'>Editora:</span> " + item.editora + "</a>")
		                	        .appendTo( ul );
								  }

		                	        
		                	    };
	});
</script>

<p>Nova Colônia</p>
<?php 
 
 echo Form::open('colonies/insert');
 
 ?>
<p>
	<label for="form_name">Nome:</label>
	<input type="text" required="required" id="form_name" name="nome" />
</p>
<p>
 	<label>Tipo de colônia:</label>
 	<input type="radio" name="tipo" value="1" id="form_tipo_publica" checked="checked"/><label for="form_tipo_publica">Colônia Pública</label>
 	 <input type="radio" name="tipo" value="0" id="form_tipo_privada" /><label for="form_tipo_privada">Colônia Privada</label>
</p> 
<p id="p_colaboradores" style="display: none">
	<label>Colaboradores</label>
	<select name="colaboradores[]" multiple="multiple">
	<?php 
	foreach ($colaboradores as $colaborador) {
		echo '<option value="'.$colaborador->id.'">'.$colaborador->profile_fields['nome'].'</option>' . "\n";
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
<input type="checkbox" checked="checked" name="notificacao" id="notificacao"/><label for="notificacao">Desejo receber notificações desta colônia</label>
</p>

<p>
	<input type="submit" value="Cadastrar" />
</p>
 <?php
 echo Form::close();
 
 echo Html::anchor('colonies/select', 'Voltar');
?>

</div>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>