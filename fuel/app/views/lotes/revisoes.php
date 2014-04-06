<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; ?>

<div id="content" style="overflow-y:scroll;">

<p>
	<h1>Revisões do Lote <?php echo $dadosLote->number;?></h1>
</p>
<br/>
<p>
Dados atuais do lote:<br/><br/>

Número: <?php echo $dadosLote->number;?><br/>
Colônia: <?php echo $dadosLote->nome_colonia;?><br/>
Linha: <?php echo $dadosLote->nome_linha;?><br/>

Núcleo: <?php echo $dadosLote->nucleu;?><br/>
Secção: <?php echo $dadosLote->section;?><br/>
Lado/Ala: <?php echo $dadosLote->edge;?><br/>

<?php 
if ($dadosLote->elevation) {
?>
Elevação média (aproximada): <?php echo str_replace('.', ',', $dadosLote->elevation);?> metros<br/>
<?php 
}

if ($dadosLote->area) {
?>
Área (real): <?php echo str_replace('.', ',', $dadosLote->area);?> hectares<br/>
<?php 
}

if (isset($dadosLote->city) && isset($dadosLote->city->state)) {
?>
Cidade atual (mais próxima): <?php echo $dadosLote->city->name . ' (' . $dadosLote->city->state->code . ')'; ?><br/>
<?php 
}
?>

Status: <?php if ($dadosLote->active == 1) { echo 'Ativo'; } else { echo 'Inativo'; }?>
</p>
<br/>

<?php

if (count($revisoes)) {
	
?>
<p>
<table>
	<tr>
		<th>Código</th>
		<th>Data e Hora</th>
		<th>Usuário</th>
		<th>Status</th>
		<th>Alterações</th>
		<th>Fontes</th>
		<th>Ações</th>
	</tr>

<?php 

	$totalDeRevisoes = count($revisoes);


	$contadorRevisao = 1;
	foreach ($revisoes as $revisao) {
		$podeReprovar = true;
?>
	<tr>
		<td>#<?php echo $revisao->id; ?></td>
		<td><?php echo $revisao->data; ?></td>
		<td><?php echo $revisao->usuario; ?></td>
		<td><?php 
		
		
		if ($revisao->approved == Model_Revision::REVISAO_APROVADA) {
			echo 'Aprovada';
		} else {
			echo 'Reprovada em ' . (!is_null($revisao->data_reprovacao)?Date::forge($revisao->data_reprovacao)->format("%d/%m/%Y %H:%M"):'-') . ' por ' . $revisao->usuario_reprovador;
		} 
		
		?></td>
		<td>
			<ul>
<?php 
		foreach ($revisao->alteracoes as $alteracao) {

			if ($contadorRevisao == $totalDeRevisoes) {
				// primeira revisão, que na verdade é a inclusão da entidade
				$podeReprovar = false;
			}
			
			$valorAntigo = $alteracao['old_value'];
			$valorNovo = $alteracao['new_value'];

			switch ($alteracao['attribute']) {
				case 'number':
					$nomeAtributo = 'Número';
					break;
				case 'edge':
					$nomeAtributo = 'Lado/Ala';
					break;
				case 'section':
					$nomeAtributo = 'Secção';
					break;
				case 'nucleu':
					$nomeAtributo = 'Núcleo';
					break;
				case 'colony_id':
					$nomeAtributo = 'Colônia';
					$valorAntigo = $alteracao['colonia_antiga'];
					$valorNovo = $alteracao['colonia_nova'];
					break;
				case 'trail_id':
					$nomeAtributo = 'Linha';
					$valorAntigo = $alteracao['linha_antiga'];
					$valorNovo = $alteracao['linha_nova'];
					break;
				case 'active':
					$nomeAtributo = 'Status';
					$valorAntigo = $alteracao['old_value']==1?'Ativo':'Inativo';
					$valorNovo = $alteracao['new_value']==1?'Ativo':'Inativo';
					break;
			}
			
			$frase = '';
			if ($podeReprovar) {
				if ($alteracao['attribute'] == 'fonte') {
					$frase = 'Inclusão de fonte';
				} else {
					$frase = $nomeAtributo . ', de "' . $valorAntigo . '" para "' . $valorNovo . '"';
				}
			} else {
				if ($alteracao['attribute'] == 'fonte') {
					$frase = 'Inclusão de fonte';
				} else {
					$frase = $nomeAtributo . ': ' . $valorNovo;
				}
			}

?>		
				<li><?php echo $frase; ?></li>
<?php 
		}
		
		foreach ($revisao->coordenadas as  $coordenadas) {

			$arrayCoordenadas[] = $coordenadas;

			?>
			<li>Coordenadas: <a href="#" onclick="mostraMapas(<?php echo ($contadorRevisao-1); ?>);">Visualizar alterações</a> <div style="display: none">de <?php echo $coordenadas['coordenadas_antigas'];?> para <?php echo $coordenadas['coordenadas_novas']; ?></div></li>
			<?php 
		}
		
?>
			</ul>
		</td>
		<td>
<?php 
		if (count($revisao->fontes)) {
			foreach ($revisao->fontes as $fonte) {
				echo 'Título: ' . $fonte['titulo'] . '<br/>';
				echo 'Autor: ' . $fonte['autor'] . '<br/>';
				echo 'Editora: ' . $fonte['editora'] . '<br/>';
				echo 'Página: ' . $fonte['pagina'] . '<br/>';
				echo 'Notas: ' . $fonte['observacao'] . '<br/><br/>';
			}
		} else {
			echo "Nenhuma fonte associada.";
		}
?>		
		</td>
		<td>
		<?php 
		if ($revisao->approved == Model_Revision::REVISAO_APROVADA && $contadorRevisao == 1 && $podeReprovar) {
			echo Html::anchor('lotes/reprovar_revisao?id=' . $revisao->id, 'Reprovar');
			
		}
		
		$contadorRevisao++;
		
		?> 
		</td>
	</tr>
<?php 
	}
	
?>
</table>
</p>


<?php 

	if (isset($arrayCoordenadas) && count($arrayCoordenadas)) {
		

		

?>

<style>
      .map-canvas {
        height: 400px;
        width: 400px;
        margin: 2px;
        padding: 2px;
        
        border: 1px solid black;
        
      }
    </style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

<script type="text/javascript">

var mostra = false;

<?php 




	foreach ($arrayCoordenadas as $indice => $a) {
		
		$coordenadasAntigas = explode(' ', $a['coordenadas_antigas']);
		$coordenadasNovas = explode(' ', $a['coordenadas_novas']);
		
		$pontoCentralAntigo = $coordenadasAntigas[0];
		$pontoCentralNovo = $coordenadasNovas[0];
		
		list($lon, $lat, $alt) = explode(',', $pontoCentralAntigo);
		$pontoCentralAntigo = array('lat' => $lat, 'lon' => $lon);
		list($lon, $lat, $alt) = explode(',', $pontoCentralNovo);
		$pontoCentralNovo = array('lat' => $lat, 'lon' => $lon);
		
		// ponto central
		echo 'var pontoCentralAntigo' . $indice . ' = new google.maps.LatLng(' . $pontoCentralAntigo['lat'] . ',' . $pontoCentralAntigo['lon'] . ');' . "\n";
		echo 'var pontoCentralNovo' . $indice . ' = new google.maps.LatLng(' . $pontoCentralNovo['lat'] . ',' . $pontoCentralNovo['lon'] . ');' . "\n";
		
		// coordenadas
		echo 'var coordenadasAntigas' . $indice . ' = [';
		foreach ($coordenadasAntigas as $cA) {
			list($lon, $lat, $alt) = explode(',', $cA);
			echo 'new google.maps.LatLng(' . $lat . ',' . $lon . '),' . "\n";	
		}
		echo '];' . "\n";
		
		echo 'var coordenadasNovas' . $indice . ' = [';
		foreach ($coordenadasNovas as $cN) {
			list($lon, $lat, $alt) = explode(',', $cN);
			echo 'new google.maps.LatLng(' . $lat . ',' . $lon . '),' . "\n";
		}
		echo '];' . "\n";
		
		
	
	}
?>

function mostraMapas(id) {

	$('#divMapaAntigo').toggle();
	$('#divMapaNovo').toggle();
	
	if (mostra == false) {
		mostra = true;

		// exibe os mapas
		
		// mapa Antigo
		var mapOptionsAntigo = {zoom: 16, center: window['pontoCentralAntigo'+id]};
		var mapAntigo = new google.maps.Map(document.getElementById('mapaAntigo'), mapOptionsAntigo);
		var poligonoAntigo = new google.maps.Polygon({
		    paths: window['coordenadasAntigas'+id],
		    strokeColor: '#FF0000',
		    strokeOpacity: 0.8,
		    strokeWeight: 2,
		    fillColor: '#FF0000',
		    fillOpacity: 0.35
		  });
		poligonoAntigo.setMap(mapAntigo);

		// mapa Novo
		var mapOptionsNovo = {zoom: 16, center: window['pontoCentralNovo'+id]};
		var mapNovo = new google.maps.Map(document.getElementById('mapaNovo'), mapOptionsNovo);  
		var poligonoNovo = new google.maps.Polygon({
		    paths: window['coordenadasNovas'+id],
		    strokeColor: 'green',
		    strokeOpacity: 0.8,
		    strokeWeight: 2,
		    fillColor: 'green',
		    fillOpacity: 0.35
		  });
		poligonoNovo.setMap(mapNovo);
		
	} else {
		mostra = false;

		// recolhe os mapas
		
	}
	
}


</script>

<div style="float:left; display:none" id="divMapaAntigo">
<div class="map-canvas" id="mapaAntigo"></div><br/>Antes
</div>
<div style="float:left; display:none" id="divMapaNovo">
<div class="map-canvas" id="mapaNovo"></div><br/>Depois
</div>
<?php 
	
		
	
	}

	
} else {

?>

<p>
Não existem revisões para este lote.
</p>
<?php 
}
?>
<p style="clear:both">
<?php 
echo Html::anchor('lotes/listagem', 'Voltar');

?> 
</p>
</div>
