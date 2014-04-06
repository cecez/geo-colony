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

      // remove todos itens do select
	  $("#select-linha").empty();
	  
      // busca as linhas
	  $.get( "../api/linhas/search", { "id_colonia": this.value }, function(data) {

			if (data[0].nome == 'nop') {
				alert('Não existem linhas para esta colônia, favor escolher outra colônia');
				return true;
			}
		  
			// popula select  
	    	$.each(data, function() {

				option = $("<option />").attr('value', this.id).text(this.nome);
		    	
	    		$("#select-linha").append(option);
	    	});

		  }  ,"json" );
	  
        
    });

    
});
</script>

<p>Novo Lote</p>
<?php 
 
 echo Form::open(array('action' => 'lotes/inserir', 'enctype' => 'multipart/form-data'));
 
 ?>
<p>
	<label for="form_numero">Número:</label>
	<input type="text" required="required" id="form_numero" name="numero" />
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
	<label for="form_nucleo">Núcleo:</label>
	<input type="text" id="form_nucleo" name="nucleo" />
</p>

<p>
	<label for="form_seccao">Secção:</label>
	<input type="text" id="form_seccao" name="seccao" />
</p>

<p>
	<label for="form_lado">Lado/Ala:</label>
	<input type="text" id="form_lado" name="lado" />
</p>

<p>
Coordenadas<br/>
<input type="file" name="arquivo" /><br/>
<p>
Instruções para geração do arquivo KML<br/><br/>

1. Abra o Google Earth<br/>
2. Aproxime através do zoom a localização do lote<br/>
3. Acesse "Adicionar > Polígono" no menu superior<br/>
4. Escreva um nome para o polígono na janela que foi aberta<br/>
5. Com a janela "Novo Polígono" ainda aberta, clique no mapa para inserir os pontos do lote<br/>
6. Ao terminar de inserir os pontos, clique no botão "OK"<br/>
7. Clique com o botão direito do mouse sobre o nome do polígono criado e selecione "Salvar lugar como"<br/>
8. Escolha um nome para o arquivo, o local onde será salvo e altere o tipo de arquivo para "Kml (*.kml)"<br/>
9. Pronto. O arquivo KML gerado pode ser utilizado no sistema <br/>
</p>
</p>

<p>
Fontes<br>			
								<label for="search-fonte-name">Buscar fonte</label>
								<input id="search-fonte-name" name="fonte_nome" type="text" />ou <a href="#" id="cadastrar-fonte">Cadastrar nova fonte</a>
</p>

<div id="fontes"></div>

<p>
<input type="checkbox" checked="checked" name="notificacao" id="notificacao"/><label for="notificacao">Desejo receber notificações deste lote</label>
</p>

<p>
	<input type="submit" value="Cadastrar" />
</p>
 <?php
 echo Form::close();
 
 echo Html::anchor('lotes/listagem', 'Voltar');
?>

</div>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>