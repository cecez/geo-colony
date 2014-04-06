<?php

use Fuel\Core\Input;
use Fuel\Core\Model;
use Orm\BelongsTo;
class Controller_Lotes extends Controller_Template
{
	
	public function before() {
	
		parent::before();
	
		// verifica se está logado
		if (!Auth::check()) Response::redirect('dashboard/index');
	
	}
	
	public function action_editar() {
		
		if (Input::post()) {
			
			
			// quando formulário é submetido
			
			$auth = Auth::instance();
				
			$novoLote = Model_Plot::find(Input::post('id'));
			
			$novoLote->number = Input::post('numero');
			$novoLote->colony_id = Input::post('colonia');
			$novoLote->trail_id = Input::post('linha');
			$novoLote->nucleu = Input::post('nucleo');
			$novoLote->section = Input::post('seccao');
			$novoLote->edge = Input::post('lado');
			$novoLote->last_editor_id = $auth->get_user_id()[1];
				
			$retorno = false;
			try {
				// tenta salvar lote no bd
				$retorno = $novoLote->save();
			} catch (Fuel\Core\Database_Exception $e) {
				echo '<pre>';
				print_r($e);
				
				die('Falha');
				
			}
			
			
			if ($retorno) {
				
				// dispara evento
				Event::trigger('notificacao.lote', array(
				'id_lote' => $novoLote->id,
				'id_usuario' => $auth->get_user_id()[1],
				'frase' => 'Os dados do lote "' . $novoLote->number . '" foram alterados'
						));
				
				// busca última revisão
				$revisao = DB::query(
						'SELECT
						 			 MAX(id) AS id
					
									FROM
						 			revisions
				
						 			WHERE
						 			user_id = '. $auth->get_user_id()[1] . '
					
									LIMIT 1'
				)->as_object('Model_Revision')->execute();
					
				if (count($revisao)) {
					$revisao = $revisao[0];
				}
				
				// se houver arquivo com coordenadas
				if (Input::file('arquivo') != null && Input::file('arquivo')['error'] == 0) {
					$coordenadas = Utilidades::processaArquivoComCoordenadas();
						
					if (count($coordenadas)) {
						
						// serializa pontos antigos
						$pontos = DB::query("SELECT CONCAT(longitude, ',', latitude) AS coordenadas FROM plot_coordinates WHERE plot_id = " . Input::post('id') . ' ORDER BY id')->execute();
						$dadosAntigos = '';
						foreach ($pontos->as_array() as $c) {
							$dadosAntigos .= ' ' . $c['coordenadas'] . ',0' ;
						}
						
						$dadosNovos = '';
						foreach ($coordenadas as $c) {
							$dadosNovos .= ' ' . $c['lon'] . ',' . $c['lat'] . ',0';
						}
						
						// armazena dados serializados
						$dados = new Model_Plot_Coordinates_Log();
						$dados->plot_id = Input::post('id');
						$dados->revision_id = $revisao->id;
						$dados->old_value = trim($dadosAntigos);
						$dados->new_value = trim($dadosNovos);
						$dados->save();
						
						// remove coordenadas atuais
						DB::query('DELETE FROM plot_coordinates WHERE plot_id = ' . Input::post('id'))->execute();
						
						// insere coordenadas
						foreach ($coordenadas as $c) {
							$ponto = new Model_Plot_Coordinate();
							$ponto->plot_id = Input::post('id');
							$ponto->latitude = $c['lat'];
							$ponto->longitude = $c['lon'];
							$ponto->save();
						}
						
						// calcula área do lote, elevação
						$novoLote->area = Utilidades::calculaAreaLote($coordenadas);
						
						$pontoMedio = Utilidades::calculaPontoCentralDoLote($coordenadas);
						$novoLote->elevation = Utilidades::calculaElevacaoPonto($pontoMedio['lat'], $pontoMedio['lon']);
						
						$idCidade = Utilidades::calculaCidadeMaisProxima($pontoMedio['lat'], $pontoMedio['lon']);
						if ($idCidade) {
							$novoLote->city_id = $idCidade;
						}
						
						$novoLote->save();
					}
						
				}
				
				// se houver fontes
				if (Input::post('fonte_titulo') != null) {
						
					
						
						// cadastra dados da revisão
						$dadoRevisao = new Model_Plot_Log();
						$dadoRevisao->plot_id = $novoLote->id;
						$dadoRevisao->revision_id = $revisao->id;
						$dadoRevisao->attribute = 'fonte';
						$dadoRevisao->save();
					
					
						// cadastra a(s) fonte(s)
						$totalDeFontes = count(Input::post('fonte_titulo'));
				
						for ($i=0; $i < $totalDeFontes; $i++) {
				
							$fonte = new Model_Fonte();
								
							if (Input::post('fonte_id')[$i] != null && Input::post('fonte_id')[$i] > 0) {
								$fonte->id = Input::post('fonte_id')[$i];
							} else {
								$fonte->usuario_id = $auth->get_user_id()[1];
								$fonte->titulo = Input::post('fonte_titulo')[$i];
								$fonte->editora = Input::post('fonte_editora')[$i];
								$fonte->autor = Input::post('fonte_autor')[$i];
								$fonte->data_de_cadastro = \DB::expr('CURRENT_TIMESTAMP');
								$fonte->save();
							}
								
							// cadastra a relação da fonte com o lote
							$loteFonte = new Model_Lote_Fonte();
							$loteFonte->lote_id = $novoLote->id;
							$loteFonte->fonte_id = $fonte->id;
							$loteFonte->revisao_id = $revisao->id;
							$loteFonte->pagina = Input::post('fonte_pagina')[$i];
							$loteFonte->observacao = Input::post('fonte_notas')[$i];
							$loteFonte->save();
								
				
						}
						
					
					
				}
					
				Response::redirect('lotes/listagem?ok');
			}
				
			Response::redirect('lotes/listagem?nok');
		} else {
			// ao carregar a página
			
			$view = View::forge('lotes/editar');
			
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
			
			// busca lote
			$lote = Model_Plot::find(Input::get('id'));
			
			// preenchendo valores
			$view->set_global('id', $lote->id);
			$view->set_global('numero', $lote->number);
			$view->set_global('lado', $lote->edge);
			$view->set_global('seccao', $lote->section);
			$view->set_global('nucleo', $lote->nucleu);
			$view->set_global('id_linha', $lote->trail_id);
			$view->set_global('id_colonia', $lote->colony_id);
			
			// busca as colônias para o select
			$colonias = DB::query(
				'SELECT 
				 c.name, c.id, c.public
				
				 FROM 
				 colonies c INNER JOIN 
				 trails t ON t.colony_id=c.id
				
				 GROUP BY
				 1,2,3
				
				ORDER BY
				c.name ASC'
			)->as_object('Model_Colony')->execute();
			
			$view->set('colonias', $colonias->as_array());
			
			// busca as linhas para o select
			$linhas = DB::query(
					'SELECT
				 	t.name, t.id
			
				 FROM
				 trails t
					
				 WHERE
				 t.colony_id = ' . $lote->colony_id . '
			
				ORDER BY
				t.name ASC'
			)->as_object('Model_Trail')->execute();
				
			$view->set('linhas', $linhas->as_array());
			
			// template
			$this->template->title = 'Lote &raquo; Edição';
			$this->template->content = $view;
			
		}
		
	}
	
	
	public function action_exportar() {
		
		// busca nome do Lote e suas coordenadas
		$dadosLote = Model_Plot::find(Input::get('id'));
		
		$pontos = DB::query("SELECT longitude, latitude FROM plot_coordinates WHERE plot_id = " . Input::get('id') . ' ORDER BY id')->execute();
		$coordenadas = $pontos->as_array();

		$stringCoordenadas = '';
		foreach ($coordenadas as $c) {
			$stringCoordenadas .= $c['longitude'] . ',' . $c['latitude'] . ',0 ';
		}
		
		$stringCoordenadas .= $coordenadas[0]['longitude'] . ',' . $coordenadas[0]['latitude'] . ',0';
		
		// abre arquivo modelo KML
		$caminhoModelo = APPPATH . '../../assets/modelo.kml';
		if (!file_exists($caminhoModelo)) {
			die('Arquivo KML modelo não encontrado em ' . $caminhoModelo);
		}
		
		// lê arquivo e realiza as substituições
		$conteudoArquivo = File::read($caminhoModelo, true);
		
		$conteudoArquivo = str_replace('[nome_lote]', $dadosLote->number, $conteudoArquivo);
		$conteudoArquivo = str_replace('[coordenadas_lote]', $stringCoordenadas, $conteudoArquivo);
		
		// cria arquivo temporário
		$diretorioArquivoTemporario = APPPATH .'tmp/';
		$nomeArquivoTemporario = uniqid() . '.kml';
		$caminhoArquivoTemporario = $diretorioArquivoTemporario . $nomeArquivoTemporario;
		File::create($diretorioArquivoTemporario, $nomeArquivoTemporario, $conteudoArquivo);
		
		// oferece download
		File::download($caminhoArquivoTemporario, $dadosLote->number . '.kml');
		
		// remove arquivo
		File::delete($caminhoArquivoTemporario);
		
		
		
	}
	
	public function action_inserir() {
		
		if (Input::post())
		{
			$auth = Auth::instance();
			
			$novoLote = new Model_Plot();
			$novoLote->number = Input::post('numero');
			$novoLote->colony_id = Input::post('colonia');
			$novoLote->trail_id = Input::post('linha');
			$novoLote->nucleu = Input::post('nucleo');
			$novoLote->section = Input::post('seccao');
			$novoLote->edge = Input::post('lado');
			$novoLote->user_id = $novoLote->last_editor_id = $auth->get_user_id()[1];
			$novoLote->active = '1';
			
				
			try {
				if ($novoLote->save()) {
					
					// gerencia notificacao
					if (Input::post('notificacao')) {
						Model_Notificacao_Lote::inscreve($novoLote->id, $auth->get_user_id()[1]);
					}
					
					// dispara evento
					Event::trigger('notificacao.linha', array(
					'id_linha' => $novoLote->trail_id,
					'id_usuario' => $auth->get_user_id()[1],
					'frase' => 'Um novo lote foi inserido na linha "[nome_linha]"'
					));
					
					// gera revisão somente com valores novos
					$revisao = new Model_Revision();
					$revisao->user_id = $auth->get_user_id()[1];
					$revisao->date = \DB::expr('CURRENT_TIMESTAMP');
					$revisao->approved = Model_Revision::REVISAO_APROVADA;
					$revisao->save();
					
					// cadastra dados da revisão
					$dadoRevisao = new Model_Plot_Log();
					$dadoRevisao->plot_id = $novoLote->id;
					$dadoRevisao->revision_id = $revisao->id;
					$dadoRevisao->attribute = 'number';
					$dadoRevisao->new_value = Input::post('numero');
					$dadoRevisao->save();
					
					// se houver arquivo com coordenadas
					if (Input::file('arquivo') != null && Input::file('arquivo')['error'] == 0) {
						$coordenadas = Utilidades::processaArquivoComCoordenadas();
						
						if (count($coordenadas)) {
							// insere coordenadas
							foreach ($coordenadas as $c) {
								$ponto = new Model_Plot_Coordinate();
								$ponto->plot_id = $novoLote->id;
								$ponto->latitude = $c['lat'];
								$ponto->longitude = $c['lon'];
								$ponto->save();
							}
							
							// calcula área do lote, elevação, cidade mais próxima
							$novoLote->area = Utilidades::calculaAreaLote($coordenadas);
							
							$pontoMedio = Utilidades::calculaPontoCentralDoLote($coordenadas);
							$novoLote->elevation = Utilidades::calculaElevacaoPonto($pontoMedio['lat'], $pontoMedio['lon']);
							
							$idCidade = Utilidades::calculaCidadeMaisProxima($pontoMedio['lat'], $pontoMedio['lon']);
							if ($idCidade) {
								$novoLote->city_id = $idCidade;
							}
							
							$novoLote->save();
						}
						
					}
					
					// se houver fontes
					if (Input::post('fonte_titulo') != null) {
						
						// cadastra a(s) fonte(s)
						$totalDeFontes = count(Input::post('fonte_titulo'));
						
						for ($i=0; $i < $totalDeFontes; $i++) {
						
							$fonte = new Model_Fonte();
							
							if (Input::post('fonte_id')[$i] != null && Input::post('fonte_id')[$i] > 0) {
								$fonte->id = Input::post('fonte_id')[$i];
							} else {
								$fonte->usuario_id = $auth->get_user_id()[1];
								$fonte->titulo = Input::post('fonte_titulo')[$i];
								$fonte->editora = Input::post('fonte_editora')[$i];
								$fonte->autor = Input::post('fonte_autor')[$i];
								$fonte->data_de_cadastro = \DB::expr('CURRENT_TIMESTAMP');
								$fonte->save();
							}
							
							// cadastra a relação da fonte com a lote
							
							$loteFonte = new Model_Lote_Fonte();
							$loteFonte->lote_id = $novoLote->id;
							$loteFonte->fonte_id = $fonte->id;
							$loteFonte->revisao_id = $revisao->id;
							$loteFonte->pagina = Input::post('fonte_pagina')[$i];
							$loteFonte->observacao = Input::post('fonte_notas')[$i];
							$loteFonte->save();
							
						
						}
						
					}
					
					Response::redirect('lotes/listagem?ok');
				}
			} catch (Database_Exception $e) {
				
				// erro geral
				$codigoDeErro = '1';
				
				// restrição de integridade número
				if (strpos($e->getMessage(), 'trail_id_number') !== false) {
					// já existe um lote com este número na linha
					$codigoDeErro = '2';
				}
				
			}
			
			Response::redirect('lotes/listagem?nok=' . $codigoDeErro);
			
		} else {
			
			$view = View::forge('lotes/inserir');
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
		
			$colonias = DB::query(
				'SELECT 
				 c.name, c.id, c.public
				
				 FROM 
				 colonies c INNER JOIN 
				 trails t ON t.colony_id=c.id
				
				 GROUP BY
				 1,2,3
					
				 ORDER BY
				 c.name ASC'
			)->as_object('Model_Colony')->execute();
			
			$view->set('colonias', $colonias->as_array());
			
			$this->template->title = 'Lotes &raquo; Inserir';
			$this->template->content = $view;
		}
	}
	
	public function action_notificacoes($idLote, $acao) {
	
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		if ($acao == 'reativar') {
			Model_Notificacao_Lote::inscreve($idLote, $idUsuario);
		} else if ($acao == 'desativar') {
			Model_Notificacao_Lote::desinscreve($idLote, $idUsuario);
		}
	
		// retorna
		Response::redirect('lotes/listagem?ok');
	
	}
	
	public function action_mudar_status() {
		
		$auth = Auth::instance();
		
		// TODO
		// trigger para aplicar alteração sobre lotes e lotes
		
		// carrega lote 
		$lote = Model_Plot::find(Input::get('id'));
		
		// altera coluna que representa estado
		if ($lote->active == '1') {
			$lote->active = '0';
		} else {
			$lote->active = '1';
		}
		
		$lote->last_editor_id = $auth->get_user_id()[1];
		
		// salva
		if ($lote->save()) {
			
			// dispara evento
			Event::trigger('notificacao.lote', array(
			'id_lote' => $lote->id,
			'id_usuario' => $auth->get_user_id()[1],
			'frase' => 'O lote "' . $lote->number . '" e seus descendentes foram ' . ($lote->active==1?'reativados':'desativados')
			));
			
			Response::redirect('lotes/listagem?ok');
		} else {
			Response::redirect('lotes/listagem?nok');
		}
	}
		
	public function action_revisoes() {
		
		$view = View::forge('lotes/revisoes');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// busca dados do lote
		$dadosLote = Model_Plot::find(Input::get('id'));
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLote->colony_id);
		
		$dadosLote->nome_colonia = $colonia->name;
		
		// busca nome da linha
		$linha = Model_Trail::find($dadosLote->trail_id);
		
		$dadosLote->nome_linha = $linha->name;
		
		// busca revisões do lote
		$dadosRevisoes = DB::query(
				'SELECT 
				 r.id, 
				 DATE_FORMAT(r.date, \'%d/%m/%Y %H:%i\') AS data, 
				 r.user_id, 
				 r.approved, 
				 u.id AS id_usuario, 
				 rep.id AS id_usuario_reprovador, 
				 r.data_reprovacao
				
				 FROM 
				 revisions r INNER JOIN 
				 users u ON r.user_id = u.id LEFT JOIN
				 users rep ON rep.id = r.usuario_reprovador_id
				
				 WHERE 
				 r.id IN (
				          SELECT 
				          DISTINCT revision_id 
				
				          FROM 
				          plots_log 
				
			    	      WHERE 
				          plot_id = ' . Input::get('id') . '
				         ) OR
				r.id IN (
						 SELECT
						 DISTINCT revision_id
				
						 FROM
						 plot_coordinates_log
				
						 WHERE
						 plot_id = ' . Input::get('id') . '
						)
				
				
				ORDER BY
				r.date DESC, r.id DESC'
		)->as_object('Model_Revision')->execute();
		
		if (count($dadosRevisoes)) {
			foreach ($dadosRevisoes as $revisao) {
				
				// busca alterações
				$alteracoesRevisao = DB::query(
					'SELECT
				     attribute, 
					 p.old_value, 
					 p.new_value, 
					 cold.name AS colonia_antiga, cnew.name AS colonia_nova, 
					 told.name AS linha_antiga, tnew.name AS linha_nova
						
					 FROM
				     plots_log p left join 
                     colonies cold on cold.id=p.old_value left join 
                     colonies cnew on cnew.id=p.new_value left join
					 trails told on told.id=p.old_value left join
					 trails tnew on tnew.id=p.new_value
						
				     WHERE
				     p.revision_id = ' . $revisao->id
				)->execute();
				
				$revisao->alteracoes = $alteracoesRevisao->as_array();
				
				// busca fontes
				$fontesRevisao = DB::query(
					'SELECT
					 f.titulo, f.autor, f.editora, lf.pagina, lf.observacao
						
					 FROM
					 lote_fonte lf INNER JOIN
					 fontes f ON f.id = lf.fonte_id
						
					 WHERE
					 lf.revisao_id = ' . $revisao->id
				)->execute();
				
				$revisao->fontes = $fontesRevisao->as_array();
				
				// busca coordenadas
				$coordenadasRevisao = DB::query(
					'SELECT
					 old_value AS coordenadas_antigas, new_value AS coordenadas_novas
				
					 FROM
					 plot_coordinates_log
				
					 WHERE
					 revision_id = ' . $revisao->id
				)->execute();
				
				$revisao->coordenadas = $coordenadasRevisao->as_array();
				
				// busca usuário
				$usuario = Model_User::find($revisao->id_usuario);
				$revisao->usuario = $usuario->profile_fields['nome'];
				
				// busca usuário reprovador
				$revisao->usuario_reprovador = '';
				if ($revisao->id_usuario_reprovador) {
					$usuario = Model_User::find($revisao->id_usuario_reprovador);
					$revisao->usuario_reprovador = $usuario->profile_fields['nome'];
				}
			}
			
		}
		
		
		
		$view->set_global('dadosLote', $dadosLote);
		$view->set_global('revisoes', $dadosRevisoes->as_array());
		
		$this->template->title = 'Lotes &raquo; Revisões';
		$this->template->content = $view;
		
	}
	
	public function action_reprovar_revisao() {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// restaura valores
		
			// busca valores
			$historico = Model_Plot_Log::query()->where('revision_id', Input::get('id'))->get();
			
			foreach ($historico as $item) {
				// busca lote
				$lote = Model_Plot::find($item->plot_id);
				$idLote = $lote->id;
				$nomeLote = $lote->number;
				// atualiza valor
				$lote->{$item->attribute} = $item->old_value;
				$lote->last_editor_id = $idUsuario;
				$lote->reprovacao = 1;
				$lote->save();
			}
			
			// coordenadas
			$coordenadas = Model_Plot_Coordinates_Log::query()->where('revision_id', Input::get('id'))->get();
				
			if (count($coordenadas)) {
			
				$coordenadas = array_pop($coordenadas);
				$lote = new stdClass();
				$lote->id = $coordenadas->plot_id;
				
				// remove valores atuais
				DB::query('DELETE FROM plot_coordinates WHERE plot_id = ' . $coordenadas->plot_id)->execute();
			
				// restaura valores antigos
				$coordenadasAntigas = explode(' ', $coordenadas->old_value);
			
				foreach ($coordenadasAntigas as $c) {
					list($lon, $lat, $alt) = explode(',', $c);
						
					$ponto = new Model_Plot_Coordinate();
					$ponto->plot_id = $coordenadas->plot_id;
					$ponto->latitude = $lat;
					$ponto->longitude = $lon;
					$ponto->save();
				}
			
			}
			
			
		
		
			
		// rejeita revisão
		$revisao = Model_Revision::find(Input::get('id'));
		$revisao->approved = '0';
		$revisao->usuario_reprovador_id = $idUsuario;
		$revisao->data_reprovacao = Date::time()->get_timestamp();
		
		
		if ($revisao->save() && isset($idLote)) {
			// dispara evento
			Event::trigger('notificacao.lote', array(
			'id_lote' => $idLote,
			'id_usuario' => $idUsuario,
			'frase' => 'A revisão #' . Input::get('id') . ' do lote "' . $nomeLote . '" foi rejeitada'
					));
		}
		
		// TODO mensagem de ok/erro	
		// redireciona de volta para listagem
		Response::redirect('lotes/revisoes?id=' . $lote->id);
		
	}
	
	public function action_listagem() {
		
		$config = array(
				'pagination_url' => Uri::create('lotes/listagem/p'),
				'per_page'       => 50,
				'uri_segment'    => 4
		);
		
		$paginacao = Pagination::forge('lotes', $config);
		
		$view = View::forge('lotes/listagem', $paginacao);
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		$where = null;
		if (Input::post()) {
			$numero_lote = Input::post('numero_lote');
			$nome_linha = Input::post('nome_linha');
			$nome_colonia = Input::post('nome_colonia');
			
			// salva consulta na sessão
			Session::set('form_lotes_numero_lote', Input::post('numero_lote'));
			Session::set('form_lotes_nome_linha', Input::post('nome_linha'));
			Session::set('form_lotes_nome_colonia', Input::post('nome_colonia'));
				
			if (!empty($numero_lote)) {
				$where[] = 'p.number LIKE \'%'.$numero_lote.'%\'';
			}
			
			if (!empty($nome_linha)) {
				$where[] = 't.name LIKE \'%'.$nome_linha.'%\'';
			}
			
			if (!empty($nome_colonia)) {
				$where[] = 'c.name LIKE \'%'.$nome_colonia.'%\'';
			}
			
			if (is_array($where)) {
				$where = implode(' AND ', $where);
			}
		} else {
			
			// paginação
			$temPaginacao = Uri::segment(3);
			
			if (is_null($temPaginacao)) {
				// remove dados da sessão, pois usuário acessou a página pela primeira vez
				Session::delete('form_lotes_numero_lote');
				Session::delete('form_lotes_nome_linha');
				Session::delete('form_lotes_nome_colonia');
			} else {
			
				// verifica se existem dados na sessão
				$numero_lote = Session::get('form_lotes_numero_lote');
				$nome_linha = Session::get('form_lotes_nome_linha');
				$nome_colonia = Session::get('form_lotes_nome_colonia');
				
				if (!empty($numero_lote)) {
					$where[] = 'p.number LIKE \'%'.$numero_lote.'%\'';
				}
				
				if (!empty($nome_linha)) {
					$where[] = 't.name LIKE \'%'.$nome_linha.'%\'';
				}
					
				if (!empty($nome_colonia)) {
					$where[] = 'c.name LIKE \'%'.$nome_colonia.'%\'';
				}
					
				if (is_array($where)) {
					$where = implode(' AND ', $where);
				}
			}
		}
		
		// conta o total de registros
		$contagemLotes = DB::query(
				'SELECT COUNT(*) as total
				 
				 FROM
				 plots p INNER JOIN
				 users u ON p.user_id = u.id INNER JOIN
				 trails t ON t.id = p.trail_id INNER JOIN
				 colonies c ON c.id = t.colony_id
		
				 '.(!empty($where)?'WHERE '.$where:'')
		)->execute()->as_array();
		
		Pagination::instance('lotes')->total_items = $contagemLotes[0]['total'];
		
		// usuario logado
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// busca todas as colônias
		$dadosLotes = DB::query(
				'SELECT
				 p.id, p.number, p.active, c.name AS nome_colonia, t.name AS nome_linha, u.username, (SELECT COUNT(DISTINCT revision_id) FROM plots_log WHERE plot_id = p.id GROUP BY plot_id) AS number_of_revisions, nl.id_usuario AS notificacao
		
				 FROM
				 plots p INNER JOIN 
				 users u ON p.user_id = u.id INNER JOIN 
				 trails t ON t.id = p.trail_id INNER JOIN 
				 colonies c ON c.id = t.colony_id LEFT JOIN 
				 notificacao_lote nl ON nl.id_lote = p.id AND nl.id_usuario = ' . $idUsuario . '
				
				 '.($where?'WHERE '.$where:'').'
		
				 ORDER BY
				 p.number ASC, t.name ASC, c.name ASC
				
				 LIMIT ' . $paginacao->per_page . '
				
				 OFFSET ' . $paginacao->offset
		)->as_object('Model_Plot')->execute();
		
		$view->set_global('lotes', $dadosLotes->as_array());
		
		// mensagem de erro
		// TODO constantes para os erros
		$mensagem = '';
		if (Input::get('nok') == '2') {
			$mensagem = 'Já existe um lote com este número nesta linha. Escolha outro número.';
		}
		
		$view->set_global('mensagemDeErro', $mensagem);
		
		$this->template->title = 'Lotes &raquo; Listagem';
		$this->template->content = $view;
	}
	
	public function action_visualizar() {
	
		$view = View::forge('lotes/visualizar');
	
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
	
		// busca dados do lote
		$dadosLote = Model_Plot::find(Input::get('id'), array(
			'related' => array(
				'city' => array('select' => array('id', 'name'), 'related' => array('state' => array('select' => array('id', 'code')))   )
				)
			)
		);
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLote->colony_id);
		
		$dadosLote->nome_colonia = $colonia->name;
		
		// busca nome da linha
		$linha = Model_Trail::find($dadosLote->trail_id);
		
		$dadosLote->nome_linha = $linha->name;
		
		// busca as fontes das revisões aprovadas do lote
		$dadosFontes = $dadosLote->buscaFontes();
		
		// busca as coordenadas do lote
		$pontos = DB::query("SELECT longitude, latitude FROM plot_coordinates WHERE plot_id = " . Input::get('id') . ' ORDER BY id')->execute();
		
		$view->set_global('coordenadas', $pontos->as_array());
		$view->set_global('dadosLote', $dadosLote);
		$view->set_global('fontes', $dadosFontes);
	
		$this->template->title = 'Lotes &raquo; Visualizar';
		$this->template->content = $view;
	
	}
}

?>