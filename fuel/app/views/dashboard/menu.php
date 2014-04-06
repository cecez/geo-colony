<div id="sidebar">
	<header>
		<h1 class="logo">Geo Colony</h1>
		<h2 class="title">Mapa das colônias <b>italianas</b> e <b>alemãs</b> no <b>Rio Grande do Sul</b></h2>
	</header>
	
	<div class="util">
		
		<p>
		
<?php

$auth = Auth::instance();

	if ($auth->get_user_id()) {

		$usuario = Auth::instance()->get_user_array();
		
		

		// usuario registrado
		echo 'Olá ' . $usuario['profile_fields']['nome'];
		$link = array(Html::anchor('users/logout', 'Logout'));
                echo Html::ul($link);
                ?>
                <div class="panel open">
                	<h3 class="header">Administração</h3>
                	<div class="content">
                	<?php echo Html::ul(array(
                							  Html::anchor('colonies/select', 'Colônias'), 
                							  Html::anchor('linhas/listagem', 'Linhas'), 
                							  Html::anchor('lotes/listagem', 'Lotes'),
                							  Html::anchor('proprietarios/listagem', 'Proprietários'),
                							  Html::anchor('usuarios/listagem', 'Usuários'),
                							  Html::anchor('usuarios/meus_dados', 'Meus dados')
                							 )
                					 ); ?>
                	<br/>
            
                	</div>
                </div>
<?php 
   	} 
?>
		
		</p>
		<?php echo Html::anchor('./', 'Retornar ao mapa'); ?>		
	</div>
	
	<footer class="footer">
		2014 - <a href="#">sobre o projeto</a>
	</footer>
	
</div>