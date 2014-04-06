<div id="sidebar">
	<header>
		<h1 class="logo">Geo Colony</h1>
		<h2 class="title">Mapa das colônias <b>italianas</b> e <b>alemãs</b> no <b>Rio Grande do Sul</b></h2>
	</header>
	
	<div class="util">
		
		<div id="lands" class="empty">
			<script type="text/html" id="lands-template">
				<li class="land" data-land_id="<%= data.id %>" style="background-color: <%= data.color %>">
					<% if (data.name) { %>
						<span class="label"><%= data.name %></span>
					<% } else { %>
						<span class="label loading">carregando...</span>
					<% } %>
					<a href="#" class="remove-link" title="remover">remover</a>
				</li>
			</script>
			<ul class="lands-list"></ul>
			<div class="buttons">
				<button type="button" class="reset-lands-button button">limpar</button>
			</div>
		</div>
		
		<form action="" id="search-trail" class="panel">
			<h3 class="header">Colônias</h3>
			<div class="content">
				<fieldset>
					<ol>
						<li>
							<?php echo Form::label('Nome da colônia', 'colony', array('for' => 'search-trail-colony')); ?>
							<?php echo Form::select('colony', null, $colonies, array('id' => 'search-trail-colony')); ?>
						</li>
						<li>
							<?php echo Form::label('Nome da linha / travessão', 'trail', array('for' => 'search-trail-trail')); ?>
							<?php echo Form::select('trail', null, $trails, array('id' => 'search-trail-trail', 'disabled' => true)); ?>
						</li>
					</ol>
				</fieldset>
				<div class="buttons">
					<button id="search-trail-button" class="button" disabled>adicionar</button>
				</div>
			</div>
		</form>
		
		<div id="search-location" class="panel">
			<h3 class="header">Localidades</h3>
			<div class="content">
				<form action="" id="search-city">
					<fieldset>
						<ol>
							<li>
								<?php echo Form::label('Cidade', 'name', array('for' => 'search-city-name')); ?>
								<?php echo Form::input('name', null, array('id' => 'search-city-name')); ?>
							</li>
						</ol>
					</fieldset>
					<div class="buttons">
						<button id="search-city-button" class="button">pesquisar</button>
					</div>
					<script type="text/html" id="search-city-result-template">
						<li class="trail">
							<a href="#" data-trail_id="<%= data.id %>" class="label"><%= data.name %></a> (<%= data.plots.length %>)
						</li>
					</script>
					<div id="search-city-result" class="result"></div>
				</form>
			</div>
		</div>
		
		<form action="" id="search-landholder" class="panel">
			<h3 class="header">Proprietários</h3>
			<div class="content">
				<fieldset>
					<ol>
						<li>
							<?php echo Form::label('Nome', 'name', array('for' => 'search-landholder-name')); ?>
							<?php echo Form::input('name', null, array('id' => 'search-landholder-name')); ?>
						</li>
					</ol>
				</fieldset>
				<div class="buttons">
					<button id="search-trail-button" class="button">pesquisar</button>
				</div>
				<script type="text/html" id="search-landholder-result-template">
					<li class="landholder">
						<a href="#" data-landholder_id="<%= data.id %>" class="label"><%= data.name %></a>
					</li>
				</script>
				<div id="search-landholder-result" class="result"></div>
			</div>
		</form>
	<p>
		
		<?php

$auth = Auth::instance();

	if ($auth->get_user_id()) {
		// usuario registrado
		echo 'Ola ' . $auth->get_screen_name();
		$link = array(Html::anchor('users/logout', 'Logout'));
                echo Html::ul($link);
               
                
	} else {
		// usuario nao registrado, exibe form de login e registro
		?>
		<div id="div-formularios-index">
		<p><br/>
		Login</p>
		<?php 
		
		if (isset($errors)) {
			echo $errors;
		}
		
		
		echo Form::open(array('action' => 'users/login', 'method' => 'post'));
	?>	
			E-mail: <input type="text" name="email" required="required"/><br/>
			Senha: <input type="password" name="senha" required="required"/></br>
			<input type="submit" value="Entrar" /> <a href="#" onclick="$('#div-formularios-index').hide(); $('#div-esqueci').show();" >Esqueci a senha</a>
		<?php 	
		echo Form::close();

		if (isset($errors_registro)) {
			echo $errors_registro;
		}
		
		echo Form::open(array('action' => 'users/registro', 'method' => 'post', 'name' => 'form_registro'));
		?>
			<p><br/>Criar novo usuário</p>
					Nome: <input type="text" name="nome" required="required"/><br/>					
					E-mail: <input type="text" name="email" required="required"/><br/>
					Senha: <input type="password" name="senha" required="required"/></br>
					<input type="submit" value="Enviar" />
				<?php 	
				echo Form::close();
				
		echo Form::open(array('action' => 'users/esqueci_a_senha', 'method' => 'post', 'name' => 'form_esqueci'));
		?>
					</div>
					<div id="div-esqueci" style="display: none">
					<p><br/>Esqueci a senha</p>
							E-mail: <input type="text" name="email" required="required"/><br/>
							<input type="submit" value="Enviar" /> <a href="#" onclick="$('#div-formularios-index').show(); $('#div-esqueci').hide();">Voltar</a>
						<?php 	
						echo Form::close();
						?>
					</div>
					<?php 

	}
?>
		
		</p>	
<?php 

if ($auth->get_user_id()) {
	// usuario registrado
	$link = array(Html::anchor('dashboard/admin', 'Retornar à administração'));
	echo Html::ul($link);
	 

}

?>		
	</div>
	
	<footer class="footer">
		2013 - <a href="#">sobre o projeto</a>
	</footer>
	
</div>


<script type="text/html" id="map-plot-window-template">
	<ul class="nav">
		<li><a href="#plot-data" class="active">Lote</a></li>
		<li><a href="#plot-landholder">Proprietário</a></li>
		<li><a href="#fontes-lote">Fontes</a></li>
	</ul>

	<div id="plot-data" class="content">
		<h1 class="title">Dados Históricos</h1>
		<ul>
			<li><span class="label">Número:</span> <%= data.number %></li>
			<li><span class="label">Colônia:</span> <%= data.colony && data.colony.name || '-' %></li>
			<li><span class="label">Linha:</span> <%= data.trail && data.trail.name || '-' %></li>
			<li><span class="label">Núcleo:</span> <%= data.nucleu || '-' %></li>
			<li><span class="label">Secção:</span> <%= data.section || '-' %></li>
			<li><span class="label">Lado/Ala:</span> <%= data.edge || '-' %></li>
		</ul>

		<h1 class="title">Dados de Geoprocessamento</h1>
		<ul>
			<li><span class="label">Área (real):</span> <%= data.area || '-' %> hectares</li>
			<li><span class="label">Cidade atual (mais próxima):</span> <%= data.cidade || '-' %></li>
			<li><span class="label">Elevação média (aproximada):</span> <%= data.elevation %> m</li>
		</ul>
	</div>

	<div id="plot-landholder" class="content hide">
		<% 
			var len = data.plot_landholders && data.plot_landholders.length
			if (len) {
		%>
			<% _.each(data.plot_landholders, function(data, i) { %>
				<h1 class="title">Proprietário <%= len - i %></h1>
				<ul>
					<li><span class="label">Nome:</span> <%= data.landholder_name || '-' %></li>
					<li><span class="label">Família:</span> <%= data.landholder_family || '-' %></li>
					<li><span class="label">Ano de concessão do lote:</span> <%= data.granting || '-' %></li>
					<li><span class="label">Ano de quitação do lote:</span> <%= data.release || '-' %></li>
					<li><span class="label">Valor do lote:</span> $ <%= data.price || '-' %></li>
					<li><span class="label">Área (informada):</span> <%= data.area || '-' %> m2</li>
				</ul>
			<% 	}) %>
		<% } else { %>
			Não foram encontradas informações a respeito dos proprietários.
		<% } %>
	</div>

	<div id="fontes-lote" class="content hide">

		<%
			var cf = data.fontes_colonia && data.fontes_colonia.length
			if (cf) {
		%>
		<h1 class="title">Colônia</h1>
		<%
				_.each(data.fontes_colonia, function(data, i) { 
		%>
		<ul>
			<li><span class="label">Título:</span> <%= data.titulo || '-' %></li>
			<li><span class="label">Autor:</span> <%= data.autor || '-' %></li>
			<li><span class="label">Editora:</span> <%= data.editora || '-' %></li>
			<li><span class="label">Página:</span> <%= data.pagina || '-' %></li>
			<li><span class="label">Notas:</span> <%= data.observacao || '-' %></li>
		</ul><br/>
		<% }) } %>

		<%
			var lf = data.fontes_linha && data.fontes_linha.length
			if (lf) {
		%>
		<h1 class="title">Linha</h1>
		<%
				_.each(data.fontes_linha, function(data, i) { 
		%>
		<ul>
			<li><span class="label">Título:</span> <%= data.titulo || '-' %></li>
			<li><span class="label">Autor:</span> <%= data.autor || '-' %></li>
			<li><span class="label">Editora:</span> <%= data.editora || '-' %></li>
			<li><span class="label">Página:</span> <%= data.pagina || '-' %></li>
			<li><span class="label">Notas:</span> <%= data.observacao || '-' %></li>
		</ul><br/>
		<% }) } %>
		
		<%
			var of = data.fontes_lote && data.fontes_lote.length
			if (of) {
		%>
		<h1 class="title">Lote</h1>
		<%
				_.each(data.fontes_lote, function(data, i) { 
		%>
		<ul>
			<li><span class="label">Título:</span> <%= data.titulo || '-' %></li>
			<li><span class="label">Autor:</span> <%= data.autor || '-' %></li>
			<li><span class="label">Editora:</span> <%= data.editora || '-' %></li>
			<li><span class="label">Página:</span> <%= data.pagina || '-' %></li>
			<li><span class="label">Notas:</span> <%= data.observacao || '-' %></li>
		</ul><br/>
		<% }) } %>
		
		<%
			var pf = data.fontes_proprietarios && data.fontes_proprietarios.length
			if (pf) {
		
				_.each(data.fontes_proprietarios, function(data, i) {
		%>
				<h1 class="title">Proprietário <%= data.nome %></h1>
		<%
					_.each(data.fontes, function(data2, i2) { 
		%>
		<ul>
			<li><span class="label">Título:</span> <%= data2.titulo || '-' %></li>
			<li><span class="label">Autor:</span> <%= data2.autor || '-' %></li>
			<li><span class="label">Editora:</span> <%= data2.editora || '-' %></li>
			<li><span class="label">Página:</span> <%= data2.pagina || '-' %></li>
			<li><span class="label">Notas:</span> <%= data2.observacao || '-' %></li>
		</ul><br/>
		<% 			}) 
				}) 
			} 
		%>		

		
	</div>
</script>

<div id="map"></div>
