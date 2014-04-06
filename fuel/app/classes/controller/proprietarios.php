<?php

use Fuel\Core\Input;
use Fuel\Core\Model;
class Controller_Proprietarios extends Controller_Template
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
				
			$novoProprietario = Model_Plot_Landholder::find(Input::post('id'));
			$novoProprietario->landholder_name = Input::post('nome');
			$novoProprietario->landholder_family = Input::post('familia');
			$novoProprietario->landholder_origin = Input::post('origem');
			$novoProprietario->granting = Input::post('concessao');
			$novoProprietario->release = Input::post('quitacao');
			$novoProprietario->area = Input::post('area');
			$novoProprietario->price = Input::post('valor');
			$novoProprietario->observation = Input::post('observacao');
			$novoProprietario->plot_id = Input::post('lote');
			$novoProprietario->last_editor_id = $auth->get_user_id()[1];
				
			if ($novoProprietario->save()) {
				
				// dispara evento
				Event::trigger('notificacao.proprietario', array(
				'id_proprietario' => $novoProprietario->id,
				'id_usuario' => $auth->get_user_id()[1],
				'frase' => 'Os dados do proprietário "' . $novoProprietario->landholder_name . '" foram alterados'
						));
				
				// se houver fontes
				if (Input::post('fonte_titulo') != null) {
						
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
						
						// cadastra dados da revisão
						$dadoRevisao = new Model_Plot_Landholder_Log();
						$dadoRevisao->plot_landholder_id = $novoProprietario->id;
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
								
							// cadastra a relação da fonte com o lote-proprietario
							$loteFonte = new Model_Lote_Proprietario_Fonte();
							$loteFonte->loteproprietario_id = $novoProprietario->id;
							$loteFonte->fonte_id = $fonte->id;
							$loteFonte->revisao_id = $revisao->id;
							$loteFonte->pagina = Input::post('fonte_pagina')[$i];
							$loteFonte->observacao = Input::post('fonte_notas')[$i];
							$loteFonte->save();
								
				
						}
						
					}
					
				}
					
				Response::redirect('proprietarios/listagem?ok');
			}
				
			Response::redirect('proprietarios/listagem?nok');
		} else {
			// ao carregar a página
			
			$view = View::forge('proprietarios/editar');
			
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
			
			// busca lote-proprietario
			$proprietario = Model_Plot_Landholder::find(Input::get('id'));
			// busca dados do lote
			$lote = Model_Plot::find($proprietario->plot_id);
			
			// preenchendo valores
			$view->set_global('id', $proprietario->id);
			$view->set_global('nome', $proprietario->landholder_name);
			$view->set_global('familia', $proprietario->landholder_family);
			$view->set_global('origem', $proprietario->landholder_origin);
			$view->set_global('concessao', $proprietario->granting);
			$view->set_global('quitacao', $proprietario->release);
			$view->set_global('area', $proprietario->area);
			$view->set_global('valor', $proprietario->price);
			$view->set_global('observacao', $proprietario->observation);
			$view->set_global('id_lote', $proprietario->plot_id);
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
			
			// busca os lotes para o select
			$lotes = DB::query(
					'SELECT
				 	p.number, p.id
		
				 FROM
				 plots p
			
				 WHERE
				 p.trail_id = ' . $lote->trail_id . '
		
				ORDER BY
				p.number ASC'
			)->as_object('Model_Trail')->execute();
			
			$view->set('lotes', $lotes->as_array());
			
			// template
			$this->template->title = 'Proprietário &raquo; Edição';
			$this->template->content = $view;
			
		}
		
	}
	
	public function action_inserir() {
		
		
		if (Input::post())
		{
			$auth = Auth::instance();
			
			$novoProprietario = new Model_Plot_Landholder();
			$novoProprietario->landholder_name = Input::post('nome');
			$novoProprietario->landholder_family = Input::post('familia');
			$novoProprietario->landholder_origin = Input::post('origem');
			$novoProprietario->granting = Input::post('concessao');
			$novoProprietario->release = Input::post('quitacao');
			$novoProprietario->area = Input::post('area');
			$novoProprietario->price = Input::post('valor');
			$novoProprietario->observation = Input::post('observacao');
			$novoProprietario->plot_id = Input::post('lote');
			$novoProprietario->user_id = $novoProprietario->last_editor_id = $auth->get_user_id()[1];
			$novoProprietario->active = '1';
			
			if ($novoProprietario->save()) {
				
				// gerencia notificacao
				if (Input::post('notificacao')) {
					Model_Notificacao_Proprietario::inscreve($novoProprietario->id, $auth->get_user_id()[1]);
				}
				
				// dispara evento
				Event::trigger('notificacao.lote', array(
				'id_lote' => $novoProprietario->plot_id,
				'id_usuario' => $auth->get_user_id()[1],
				'frase' => 'Um novo proprietário foi inserido no lote "[nome_lote]"'
						));
				
				// gera revisão somente com valores novos
				$revisao = new Model_Revision();
				$revisao->user_id = $auth->get_user_id()[1];
				$revisao->date = \DB::expr('CURRENT_TIMESTAMP');
				$revisao->approved = Model_Revision::REVISAO_APROVADA;
				$revisao->save();
				
				// cadastra dados da revisão
				$dadoRevisao = new Model_Plot_Landholder_Log();
				$dadoRevisao->plot_landholder_id = $novoProprietario->id;
				$dadoRevisao->revision_id = $revisao->id;
				$dadoRevisao->attribute = 'name';
				$dadoRevisao->new_value = Input::post('nome');
				$dadoRevisao->save();
				
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
						
						// cadastra a relação da fonte com a Proprietario
						
						$proprietarioFonte = new Model_Lote_Proprietario_Fonte();
						$proprietarioFonte->loteproprietario_id = $novoProprietario->id;
						$proprietarioFonte->fonte_id = $fonte->id;
						$proprietarioFonte->revisao_id = $revisao->id;
						$proprietarioFonte->pagina = Input::post('fonte_pagina')[$i];
						$proprietarioFonte->observacao = Input::post('fonte_notas')[$i];
						$proprietarioFonte->save();
						
					
					}
					
				}
				
				Response::redirect('proprietarios/listagem?ok');
			}
			
			Response::redirect('proprietarios/listagem?nok');
			
		} else {
			
			$view = View::forge('proprietarios/inserir');
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
			
			$this->template->title = 'Proprietarios &raquo; Inserir';
			$this->template->content = $view;
		}
	}
	
	public function action_notificacoes($idProprietario, $acao) {
	
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		if ($acao == 'reativar') {
			Model_Notificacao_Proprietario::inscreve($idProprietario, $idUsuario);
		} else if ($acao == 'desativar') {
			Model_Notificacao_Proprietario::desinscreve($idProprietario, $idUsuario);
		}
	
		// retorna
		Response::redirect('proprietarios/listagem?ok');
	
	}
	
	public function action_mudar_status() {
		
		$auth = Auth::instance();
		
		// TODO
		// trigger para aplicar alteração sobre proprietarios e proprietarios
		
		// carrega lote 
		$lote = Model_Plot_Landholder::find(Input::get('id'));
		
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
			Event::trigger('notificacao.proprietario', array(
			'id_proprietario' => $lote->id,
			'id_usuario' => $auth->get_user_id()[1],
			'frase' => 'O proprietário "' . $lote->landholder_name . '" foi ' . ($lote->active==1?'reativado':'desativado')
			));
			
			Response::redirect('proprietarios/listagem?ok');
		} else {
			Response::redirect('proprietarios/listagem?nok');
		}
	}
		
	public function action_revisoes() {
		
		$view = View::forge('proprietarios/revisoes');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// busca dados do lote-proprietario
		$dados = Model_Plot_Landholder::find(Input::get('id'));
		
		// busca dados do lote
		$dadosLote = Model_Plot::find($dados->plot_id);
		$dados->numero_lote = $dadosLote->number;
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLote->colony_id);
		$dados->nome_colonia = $colonia->name;
		
		// busca nome da linha
		$linha = Model_Trail::find($dadosLote->trail_id);
		$dados->nome_linha = $linha->name;
		
		// busca revisões do lote-proprietario
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
				          plot_landholders_log 
				
			    	      WHERE 
				          plot_landholder_id = ' . Input::get('id') . '
				         )
				
				ORDER BY
				r.date DESC, r.id DESC'
		)->as_object('Model_Revision')->execute();
		
		if (count($dadosRevisoes)) {
			foreach ($dadosRevisoes as $revisao) {
				
				// busca alterações
				$alteracoesRevisao = DB::query(
					'SELECT
				     attribute, old_value, new_value, cold.name AS colonia_antiga, cnew.name AS colonia_nova, told.name AS linha_antiga, tnew.name AS linha_nova, pold.number AS lote_antigo, pnew.number AS lote_novo
						
					 FROM
				     plot_landholders_log ph left join 
					 plots pold on pold.id=ph.old_value left join
					 plots pnew on pnew.id=ph.new_value left join
                     colonies cold on cold.id=pold.colony_id left join 
                     colonies cnew on cnew.id=pnew.colony_id left join
					 trails told on told.id=pold.trail_id left join
					 trails tnew on tnew.id=pnew.trail_id
						
				     WHERE
				     revision_id = ' . $revisao->id
				)->execute();
				
				$revisao->alteracoes = $alteracoesRevisao->as_array();
				
				// busca fontes
				$fontesRevisao = DB::query(
					'SELECT
					 f.titulo, f.autor, f.editora, lf.pagina, lf.observacao
						
					 FROM
					 lote_proprietario_fonte lf INNER JOIN
					 fontes f ON f.id = lf.fonte_id
						
					 WHERE
					 lf.revisao_id = ' . $revisao->id
				)->execute();
				
				$revisao->fontes = $fontesRevisao->as_array();
				
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
		
		$view->set_global('dados', $dados);
		$view->set_global('revisoes', $dadosRevisoes->as_array());
		
		$this->template->title = 'Proprietarios &raquo; Revisões';
		$this->template->content = $view;
		
	}
	
	public function action_reprovar_revisao() {
		
		// restaura valores
		
			// busca valores
			$historico = Model_Plot_Landholder_Log::query()->where('revision_id', Input::get('id'))->get();
			
			foreach ($historico as $item) {
				// busca lote
				$loteProprietario = Model_Plot_Landholder::find($item->plot_landholder_id);
				$idProprietario = $loteProprietario->id;
				$nomeProprietario = $loteProprietario->landholder_name;
				// atualiza valor
				$loteProprietario->{$item->attribute} = $item->old_value;
				$loteProprietario->last_editor_id = null;
				$loteProprietario->save();
			}
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
			
		// rejeita revisão
		$revisao = Model_Revision::find(Input::get('id'));
		$revisao->approved = '0';
		$revisao->usuario_reprovador_id = $idUsuario;
		$revisao->data_reprovacao = Date::time()->get_timestamp();
		
		if ($revisao->save() && isset($idProprietario)) {
			// dispara evento
			Event::trigger('notificacao.proprietario', array(
			'id_proprietario' => $idProprietario,
			'id_usuario' => $idUsuario,
			'frase' => 'A revisão #' . Input::get('id') . ' do proprietário "' . $nomeProprietario . '" foi rejeitada'
					));
		}
		
		// TODO mensagem de ok/erro	
		// redireciona de volta para listagem
		Response::redirect('proprietarios/revisoes?id=' . $loteProprietario->id);
		
	}
	
	public function action_listagem() {
		
		$config = array(
				'pagination_url' => Uri::create('proprietarios/listagem/p'),
				'per_page'       => 50,
				'uri_segment'    => 4
		);
		
		$paginacao = Pagination::forge('proprietarios', $config);
		
		$view = View::forge('proprietarios/listagem', $paginacao);
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		$where = null;
		if (Input::post()) {
			$proprietario = Input::post('proprietario');
			$numero_lote = Input::post('numero_lote');
			$nome_linha = Input::post('nome_linha');
			$nome_colonia = Input::post('nome_colonia');
			
			// salva consulta na sessão
			Session::set('form_proprietarios_proprietario', Input::post('proprietario'));
			Session::set('form_proprietarios_numero_lote', Input::post('numero_lote'));
			Session::set('form_proprietarios_nome_linha', Input::post('nome_linha'));
			Session::set('form_proprietarios_nome_colonia', Input::post('nome_colonia'));
				
			if (!empty($proprietario)) {
				$where[] = '(ph.landholder_name LIKE \'%'.$proprietario.'%\' OR ph.landholder_family LIKE \'%'.$proprietario.'%\' OR ph.landholder_origin LIKE \'%'.$proprietario.'%\')';
			}
			
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
				Session::delete('form_proprietarios_proprietario');
				Session::delete('form_proprietarios_numero_lote');
				Session::delete('form_proprietarios_nome_linha');
				Session::delete('form_proprietarios_nome_colonia');
			} else {
			
				// verifica se existem dados na sessão
				$proprietario = Session::get('form_proprietarios_proprietario');
				$numero_lote = Session::get('form_proprietarios_numero_lote');
				$nome_linha = Session::get('form_proprietarios_nome_linha');
				$nome_colonia = Session::get('form_proprietarios_nome_colonia');
				
				if (!empty($proprietario)) {
					$where[] = '(ph.landholder_name LIKE \'%'.$proprietario.'%\' OR ph.landholder_family LIKE \'%'.$proprietario.'%\' OR ph.landholder_origin LIKE \'%'.$proprietario.'%\')';
				}
				
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
		$contagemProprietarios = DB::query(
				'SELECT COUNT(*) as total
				 
				 FROM
				 plot_landholders ph INNER JOIN
				 users u ON ph.user_id = u.id INNER JOIN 
				 plots p ON p.id = ph.plot_id INNER JOIN 
				 trails t ON t.id = p.trail_id INNER JOIN
				 colonies c ON c.id = t.colony_id
		
				 '.(!empty($where)?'WHERE '.$where:'')
		)->execute()->as_array();
		
		Pagination::instance('proprietarios')->total_items = $contagemProprietarios[0]['total'];
		
		// usuario logado
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// busca todas as colônias
		$dadosProprietarios = DB::query(
				'SELECT
				 ph.id, ph.landholder_name, ph.landholder_family, ph.landholder_origin, ph.active, p.number AS numero_lote, c.name AS nome_colonia, t.name AS nome_linha, u.username, (SELECT COUNT(DISTINCT revision_id) FROM plot_landholders_log WHERE plot_landholder_id = ph.id GROUP BY plot_landholder_id) AS number_of_revisions, np.id_usuario AS notificacao
		
				 FROM
				 plot_landholders ph INNER JOIN 
				 users u ON ph.user_id = u.id INNER JOIN
				 plots p ON p.id = ph.plot_id INNER JOIN 
				 trails t ON t.id = p.trail_id INNER JOIN 
				 colonies c ON c.id = t.colony_id LEFT JOIN 
				 notificacao_proprietario np ON np.id_proprietario = ph.id AND np.id_usuario = ' . $idUsuario . '
				
				 '.($where?'WHERE '.$where:'').'
		
				 ORDER BY
				 ph.landholder_name ASC, t.name ASC, c.name ASC
				
				 LIMIT ' . $paginacao->per_page . '
				
				 OFFSET ' . $paginacao->offset
		)->as_object('Model_Plot')->execute();
		
		$view->set_global('proprietarios', $dadosProprietarios->as_array());
		
		$this->template->title = 'Proprietarios &raquo; Listagem';
		$this->template->content = $view;
	}
	
	public function action_visualizar() {
	
		$view = View::forge('proprietarios/visualizar');
	
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
	
		// busca dados do lote-proprietario
		$dadosProprietario = Model_Plot_Landholder::find(Input::get('id'));
		
		// busca dados do lote
		$dadosLote = Model_Plot::find($dadosProprietario->plot_id);
		
		$dadosProprietario->numero_lote = $dadosLote->number;
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLote->colony_id);
		
		$dadosProprietario->nome_colonia = $colonia->name;
		
		// busca nome da linha
		$linha = Model_Trail::find($dadosLote->trail_id);
		
		$dadosProprietario->nome_linha = $linha->name;
		
		// busca as fontes das revisões aprovadas do proprietario
		$dadosFontes = $dadosProprietario->buscaFontes();
	
		$view->set_global('dadosProprietario', $dadosProprietario);
		$view->set_global('fontes', $dadosFontes);
	
		$this->template->title = 'Proprietarios &raquo; Visualizar';
		$this->template->content = $view;
	
	}
}

?>