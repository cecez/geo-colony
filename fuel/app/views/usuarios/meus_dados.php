<?php echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<p>Meus dados</p>

<?php 

if ($mensagem) {
	echo $mensagem;
}

echo $form;

?>
<p>Deixe os campos de senha em branco caso não queira atualizar sua senha.</p>
</div>
