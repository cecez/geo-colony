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

    // busca de linhas a partir da colônia
    $('#select-colonia').change(function() {

      // remove todos itens do select da linha e lote
	  $("#select-linha").empty();
	  $("#select-lote").empty();

	  option = $("<option />").attr('value', '0').text('Selecione uma linha');
  	
	  $("#select-lote").append(option);
	  
      // busca as linhas
	  $.get( "../api/linhas/search", { "id_colonia": this.value }, function(data) {

			if (data[0].nome == 'nop') {
				alert('Não existem linhas para esta colônia, favor escolher outra colônia');
				return true;
			}

			option = $("<option />").attr('value', '0').text(' -- Selecione -- ');
	    	
    		$("#select-linha").append(option);
		  
			// popula select  
	    	$.each(data, function() {

				option = $("<option />").attr('value', this.id).text(this.nome);
		    	
	    		$("#select-linha").append(option);
	    	});

		  }  ,"json" );
	  
        
    });

 	// busca de lotes a partir da linha
    $('#select-linha').change(function() {

      // remove todos itens do select
	  $("#select-lote").empty();
	  
      // busca as linhas
	  $.get( "../api/lotes/search", { "id_linha": this.value }, function(data) {

			if (data[0].nome == 'nop') {
				alert('Não existem lotes para esta linha, favor escolher outra linha');
				return true;
			}

			// popula select  
	    	$.each(data, function() {

				option = $("<option />").attr('value', this.id).text(this.numero);
		    	
	    		$("#select-lote").append(option);
	    	});

		  }  ,"json" );
	  
        
    });

    
});
</script>

<p>Novo Proprietário</p>
<?php 
 
 echo Form::open('proprietarios/inserir');
 
 ?>
<p>
	<label for="form_nome">Nome:</label>
	<input type="text" required="required" id="form_nome" name="nome" />
</p>

<p>
	<label for="form_familia">Família:</label>
	<input type="text" required="required" id="form_familia" name="familia" />
</p>

<p>
	<label for="form_origem">Origem:</label>
	<input type="text" required="required" id="form_origem" name="origem" />
</p>

<p>
	<label>Colônia</label>
	<select name="colonia" id="select-colonia">
		<option></option>
	<?php 
	foreach ($colonias as $colonia) {
		echo '<option value="'.$colonia['id'].'">'.$colonia['name'] . ($colonia['public']=='1'?'':' [privada]').'</option>' . "\n";
	}
	?>
	</select>
</p>

<p>
	<label>Linha</label>
	<select name="linha" id="select-linha">
		<option>Selecione uma colônia</option>
	</select>
</p>

<p>
	<label>Lote</label>
	<select name="lote" id="select-lote">
		<option>Selecione uma linha</option>
	</select>
</p>

<p>
	<label for="form_concessao">Ano de concessão:</label>
	<input type="text" id="form_concessao" name="concessao" />
</p>

<p>
	<label for="form_quitacao">Ano de quitação:</label>
	<input type="text" id="form_quitacao" name="quitacao" />
</p>

<p>
	<label for="form_area">Área informada:</label>
	<input type="text" id="form_area" name="area" />
</p>

<p>
	<label for="form_valor">Valor do lote:</label>
	<input type="text" id="form_valor" name="valor" />
</p>

<p>
	<label for="form_observacao">Observação</label>
	<textarea rows="10" cols="50" id="form_observacao" name="observacao"></textarea>
</p>

<p>
Fontes<br>			
								<label for="search-fonte-name">Buscar fonte</label>
								<input id="search-fonte-name" name="fonte_nome" type="text" />ou <a href="#" id="cadastrar-fonte">Cadastrar nova fonte</a>
</p>

<div id="fontes"></div>

<p>
<input type="checkbox" checked="checked" name="notificacao" id="notificacao"/><label for="notificacao">Desejo receber notificações deste proprietário</label>
</p>

<p>
	<input type="submit" value="Cadastrar" />
</p>
 <?php
 echo Form::close();
 
 echo Html::anchor('proprietarios/listagem', 'Voltar');
?>

</div>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>