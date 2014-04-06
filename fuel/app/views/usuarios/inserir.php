<?php echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<p>Novo usuário</p>
<?php 
 
if (isset($errors)) {
	echo $errors;
}

 echo $reg;
 
 echo Html::anchor('usuarios/listagem', 'Voltar');
?>

</div>
