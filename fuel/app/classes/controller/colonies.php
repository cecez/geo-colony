<?php

use Fuel\Core\Input;
use Fuel\Core\Model;
class Controller_Colonies extends Controller_Template
{
	
	const COLONIA_PRIVADA = '0';
	const COLONIA_PUBLICA = '1';
	
	public function before() {
		
		parent::before();
		
		// verifica se está logado
		if (!Auth::check()) Response::redirect('dashboard/index');
		
	}

	
	public function action_edit() {
		
		if (Input::post()) {
			// quando formulário é submetido
			
			$auth = Auth::instance();
				
			$novaColonia = Model_Colony::find(Input::post('id'));
			$novaColonia->name = Input::post('nome');
			$novaColonia->public = Input::post('tipo');
			$novaColonia->last_editor_id = $auth->get_user_id()[1];
			$novaColonia->reprovacao = 0;
				
			if ($novaColonia->save()) {
				
				// gerencia notificacao
				if (Input::post('notificacao')) {
					Model_Notificacao_Colonia::inscreve($novaColonia->id, $auth->get_user_id()[1]);
				}
				
				// dispara evento
				Event::trigger('notificacao.colonia', array(
														'id_colonia' => $novaColonia->id,
														'id_usuario' => $auth->get_user_id()[1],
														'frase' => 'Os dados da colônia "' . $novaColonia->name . '" foram alterados'
													  ));
				
				// remove colaboradores
				$busca_colaboradores = Model_Colony_Users::query()->select('user_id')->where('colony_id', Input::post('id'))->get();
					
				foreach ($busca_colaboradores as $colaborador) {
					$colaborador->delete();
				}
					
				// colaboradores
				if (Input::post('tipo') == self::COLONIA_PRIVADA && (count(Input::post('colaboradores')) > 0)) {
						
						
					foreach (Input::post('colaboradores') as $idUsuario) {
						$colaborador = new Model_Colony_Users();
						$colaborador->colony_id = Input::post('id');
						$colaborador->user_id = $idUsuario;
						$colaborador->save();
					}
						
				}
				
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
						$dadoRevisao = new Model_Colonies_Log();
						$dadoRevisao->colony_id = $novaColonia->id;
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
								
							// cadastra a relação da fonte com a colônia
							$coloniaFonte = new Model_Colonia_Fonte();
							$coloniaFonte->colonia_id = $novaColonia->id;
							$coloniaFonte->fonte_id = $fonte->id;
							$coloniaFonte->revisao_id = $revisao->id;
							$coloniaFonte->pagina = Input::post('fonte_pagina')[$i];
							$coloniaFonte->observacao = Input::post('fonte_notas')[$i];
							$coloniaFonte->save();
								
				
						}
						
					}
					
				}
					
				Response::redirect('colonies/select?ok');
			}
				
			Response::redirect('colonies/select?nok');
		} else {
			// ao carregar a página
			
			$view = View::forge('colonies/edit');
			
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
			
			// busca colônia
			$colonia = Model_Colony::find(Input::get('id'));
			
			// preenchendo valores
			$view->set_global('id', $colonia->id);
			$view->set_global('nome', $colonia->name);
			$view->set_global('publica', $colonia->public);
			
			$colaboradores = Model_User::find('all', array('select' => array('username', 'id', 'profile_fields')));
			
			$view->set('colaboradores', $colaboradores);

			// preenche colaboradores
			$array_colaboradores = array();
			if ($colonia->public == self::COLONIA_PRIVADA) {
			
				// busca colaboradores da colônia
				$busca_colaboradores = Model_Colony_Users::query()->select('user_id')->where('colony_id', Input::get('id'))->get();
			
				foreach ($busca_colaboradores as $colaborador) {
					$array_colaboradores[] = $colaborador->user_id;
				}
				
			}
			
			$view->set('colaboradores_colonia', $array_colaboradores);
			
			// template
			$this->template->title = 'Colônias &raquo; Edição';
			$this->template->content = $view;
			
		}
		
	}
	
	public function action_insert() {
		
		
		if (Input::post())
		{
			$auth = Auth::instance();
			
			$novaColonia = new Model_Colony();
			$novaColonia->name = Input::post('nome');
			$novaColonia->public = Input::post('tipo');
			$novaColonia->user_id = $novaColonia->last_editor_id = $auth->get_user_id()[1];
			$novaColonia->active = '1';
			
			if ($novaColonia->save()) {
				
				// gerencia notificacao
				if (Input::post('notificacao')) {
					Model_Notificacao_Colonia::inscreve($novaColonia->id, $auth->get_user_id()[1]);
				}
					
				// colaboradores
				if (Input::post('tipo') == self::COLONIA_PRIVADA && (count(Input::post('colaboradores')) > 0)) {
			
					
					foreach (Input::post('colaboradores') as $idUsuario) {
						$colaborador = new Model_Colony_Users();
						$colaborador->colony_id = $novaColonia->id;
						$colaborador->user_id = $idUsuario;
						$colaborador->save();
					}
			
				}
				
				// gera revisão somente com valores novos
				$revisao = new Model_Revision();
				$revisao->user_id = $auth->get_user_id()[1];
				$revisao->date = \DB::expr('CURRENT_TIMESTAMP');
				$revisao->approved = Model_Revision::REVISAO_APROVADA;
				$revisao->save();
				
				// cadastra dados da revisão
				$dadoRevisao = new Model_Colonies_Log();
				$dadoRevisao->colony_id = $novaColonia->id;
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
						
						// cadastra a relação da fonte com a colônia
						$coloniaFonte = new Model_Colonia_Fonte();
						$coloniaFonte->colonia_id = $novaColonia->id;
						$coloniaFonte->fonte_id = $fonte->id;
						$coloniaFonte->revisao_id = $revisao->id;
						$coloniaFonte->pagina = Input::post('fonte_pagina')[$i];
						$coloniaFonte->observacao = Input::post('fonte_notas')[$i];
						$coloniaFonte->save();
						
					
					}
					
				}
				
				Response::redirect('colonies/select?ok');
			}
			
			Response::redirect('colonies/select?nok');
			
		} else {
			
			$view = View::forge('colonies/insert');
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
		
			$colaboradores = Model_User::find('all', array('select' => array('username', 'id', 'profile_fields')));
		
			$view->set('colaboradores', $colaboradores);
			
			$this->template->title = 'Usuario &raquo; Inserir Colônia';
			$this->template->content = $view;
		}
	}
	
	public function action_toggle_status() {
		
		$auth = Auth::instance();
		
		// TODO
		// trigger para aplicar alteração sobre linhas e lotes
		
		// carrega colônia 
		$new = Model_Colony::find(Input::get('id'));
		
		// altera coluna que representa estado
		if ($new->active == '1') {
			$new->active = '0';
		} else {
			$new->active = '1';
		}
		
		$new->last_editor_id = $auth->get_user_id()[1];
		
		// salva
		if ($new->save()) {
			
			// dispara evento
			Event::trigger('notificacao.colonia', array(
													'id_colonia' => $new->id,
													'id_usuario' => $auth->get_user_id()[1],
													'frase' => 'A colônia "' . $new->name . '" e seus descendentes foram ' . ($new->active==1?'reativados':'desativados')
												  ));
													
			Response::redirect('colonies/select?ok');
		}
	}
	
	public function action_notificacoes($idColonia, $acao) {
	
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		if ($acao == 'reativar') {
			Model_Notificacao_Colonia::inscreve($idColonia, $idUsuario);
		} else if ($acao == 'desativar') {
			Model_Notificacao_Colonia::desinscreve($idColonia, $idUsuario);
		}
		
		// retorna
		Response::redirect('colonies/select?ok');
		
	}
		
	public function action_revisions() {
		
		$view = View::forge('colonies/revisions');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// busca dados da colônia
		$dadosColonia = Model_Colony::find(Input::get('id'));
		
		// busca revisões da colônia
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
				          colonies_log 
				
			    	      WHERE 
				          colony_id = ' . Input::get('id') . '
				         )
				
				ORDER BY
				r.date DESC, r.id DESC'
		)->as_object('Model_Revision')->execute();
		
		if (count($dadosRevisoes)) {
			foreach ($dadosRevisoes as $revisao) {
				
				// busca alterações
				$alteracoesRevisao = DB::query(
					'SELECT
				     attribute, old_value, new_value
						
					 FROM
				     colonies_log
						
				     WHERE
				     revision_id = ' . $revisao->id
				)->execute();
				
				$revisao->alteracoes = $alteracoesRevisao->as_array();
				
				// busca fontes
				$fontesRevisao = DB::query(
					'SELECT
					 f.titulo, f.autor, f.editora, cf.pagina, cf.observacao
						
					 FROM
					 colonia_fonte cf INNER JOIN
					 fontes f ON f.id = cf.fonte_id
						
					 WHERE
					 cf.revisao_id = ' . $revisao->id
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
		
		$view->set_global('dadosColonia', $dadosColonia);
		$view->set_global('revisoes', $dadosRevisoes->as_array());
		
		$this->template->title = 'Colônias &raquo; Revisões';
		$this->template->content = $view;
		
	}
	
	public function action_revision_reject() {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// restaura valores
		
			// busca valores
			$historico = Model_Colonies_Log::query()->where('revision_id', Input::get('id'))->get();
			
			foreach ($historico as $item) {
				// busca colônia
				$colonia = Model_Colony::find($item->colony_id);
				$idColonia = $colonia->id;
				$nomeColonia = $colonia->name;
				// atualiza valor
				$colonia->{$item->attribute} = $item->old_value;
				$colonia->last_editor_id = $idUsuario;
				$colonia->reprovacao = 1;
				$colonia->save();
			}
		
		
			
		// rejeita revisão
		$revisao = Model_Revision::find(Input::get('id'));
		$revisao->approved = '0';
		$revisao->usuario_reprovador_id = $idUsuario;
		$revisao->data_reprovacao = Date::time()->get_timestamp();
		if ($revisao->save() && isset($idColonia)) {
			// dispara evento
			Event::trigger('notificacao.colonia', array(
												   'id_colonia' => $idColonia,
												   'id_usuario' => $idUsuario,
												   'frase' => 'A revisão #' . Input::get('id') . ' da colônia "' . $nomeColonia . '" foi rejeitada'
												  ));
		}
		
		// TODO mensagem de ok/erro	
		// redireciona de volta para listagem
		Response::redirect('colonies/revisions?id=' . $colonia->id);
		
	}
	
	public function action_select() {
		
		$view = View::forge('colonies/select');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// usuario logado
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// busca todas as colônias
		$dadosColonias = DB::query(
				'SELECT
				 c.id, c.name, c.public, c.active, u.username, (SELECT COUNT(DISTINCT revision_id) FROM colonies_log WHERE colony_id = c.id GROUP BY colony_id) AS number_of_revisions, nc.id_usuario AS notificacao
		
				 FROM
				 colonies c INNER JOIN 
				 users u ON c.user_id = u.id LEFT JOIN 
				 notificacao_colonia nc ON nc.id_colonia = c.id AND nc.id_usuario = ' . $idUsuario . '
		
				ORDER BY
				c.name ASC'
		)->as_object('Model_Colony')->execute();
		
		$view->set_global('colonies', $dadosColonias->as_array());
		
		$this->template->title = 'Colônias &raquo; Listagem';
		$this->template->content = $view;
	}
	
	public function action_visualizar() {
	
		$view = View::forge('colonies/visualizar');
	
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
	
		// busca dados da colônia, para os dados atuais
		$dadosColonia = Model_Colony::find(Input::get('id'));
		
		// busca as fontes das revisões aprovadas da colônia
		$dadosFontes = $dadosColonia->buscaFontes();
	
		$view->set_global('dadosColonia', $dadosColonia);
		$view->set_global('fontes', $dadosFontes);
	
		$this->template->title = 'Colônias &raquo; Visualizar';
		$this->template->content = $view;
	
	}
}

?>