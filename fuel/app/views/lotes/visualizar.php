<?php use Fuel\Core\Model;
use Fuel\Core\Controller;
echo $menu; 


?>

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

if (count($coordenadas)) {

?>
<style>
      #map-canvas {
        height: 600px;
        width: 600px;
        margin: 0px;
        padding: 0px
      }
    </style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
// exibindo o polígono no mapa
function initialize() {
  var mapOptions = {
    zoom: 10,
    center: new google.maps.LatLng(<?php echo $coordenadas[0]['latitude'] . ',' . $coordenadas[0]['longitude']; ?>)
  };

  var poligono;

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  // Define the LatLng coordinates for the polygon's path.
  var coordenadas = [
<?php 

	foreach ($coordenadas as $c) {
		echo 'new google.maps.LatLng(' . $c['latitude'] . ',' . $c['longitude'] . '),' . "\n";
	}

?>
                        
    
  ];

  // Construct the polygon.
  poligono = new google.maps.Polygon({
    paths: coordenadas,
    strokeColor: '#FF0000',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0.35
  });

  poligono.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
    Localização: <?php echo Html::anchor('lotes/exportar?id=' . $dadosLote->id, 'Exportar lote para KML'); ?>
<div id="map-canvas"></div>
<?php 

}


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
Não existem fontes associadas a este lote.
</p>
<?php 
}

echo Html::anchor('lotes/listagem', 'Voltar');

?> 

</div>
