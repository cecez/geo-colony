<?php
return array(
	'_root_'                             => 'dashboard/index',
	'_404_'                              => 'dashboard/404',
     
    'api/colonies/:id/search/trails'     => 'api/colonies/search_trails',
	'api/cities/search/trails'           => 'api/cities/search_trails',
	'api/:controller/search'             => 'api/$1/search',
    'api/:controller/:id'                => 'api/$1/id',
		
	'recuperacao/(:any)/(:any)' 		 => 'dashboard/recuperacao/$1/$2',
	'confirmacao/(:any)/(:any)' 		 => 'dashboard/confirmacao/$1/$2',

	'qgram_s7j3n1a8p4j2n3s2/landholders' => 'qgram/landholders'
);