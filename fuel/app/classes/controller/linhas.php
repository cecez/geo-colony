<?php

use Fuel\Core\Input;
use Fuel\Core\Model;
class Controller_Linhas extends Controller_Template
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
				
			$novaLinha = Model_Trail::find(Input::post('id'));
			$novaLinha->name = Input::post('nome');
			$novaLinha->colony_id = Input::post('colonia');
			$novaLinha->last_editor_id = $auth->get_user_id()[1];
				
			if ($novaLinha->save()) {
				
				// dispara evento
				Event::trigger('notificacao.linha', array(
														'id_linha' => $novaLinha->id,
														'id_usuario' => $auth->get_user_id()[1],
														'frase' => 'Os dados da linha "' . $novaLinha->name . '" foram alterados'
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
						$dadoRevisao = new Model_Trail_Log();
						$dadoRevisao->trail_id = $novaLinha->id;
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
								
							// cadastra a relação da fonte com a linha
							$linhaFonte = new Model_Linha_Fonte();
							$linhaFonte->linha_id = $novaLinha->id;
							$linhaFonte->fonte_id = $fonte->id;
							$linhaFonte->revisao_id = $revisao->id;
							$linhaFonte->pagina = Input::post('fonte_pagina')[$i];
							$linhaFonte->observacao = Input::post('fonte_notas')[$i];
							$linhaFonte->save();
								
				
						}
						
					}
					
				}
					
				Response::redirect('linhas/listagem?ok');
			}
				
			Response::redirect('linhas/listagem?nok');
		} else {
			// ao carregar a página
			
			$view = View::forge('linhas/editar');
			
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
			
			// busca linha
			$linha = Model_Trail::find(Input::get('id'));
			
			// preenchendo valores
			$view->set_global('id', $linha->id);
			$view->set_global('nome', $linha->name);
			$view->set_global('id_colonia', $linha->colony_id);
			
			$colonias = DB::query(
				'SELECT 
				 c.name, c.id, c.public
				
				 FROM 
				 colonies c
				
				ORDER BY
				c.name ASC'
			)->as_object('Model_Colony')->execute();
			
			$view->set('colonias', $colonias->as_array());
			
			// template
			$this->template->title = 'Linha &raquo; Edição';
			$this->template->content = $view;
			
		}
		
	}
	
	public function action_inserir() {
		
		
		if (Input::post())
		{
			$auth = Auth::instance();
			
			$novaLinha = new Model_Trail();
			$novaLinha->name = Input::post('nome');
			$novaLinha->colony_id = Input::post('colonia');
			$novaLinha->user_id = $novaLinha->last_editor_id = $auth->get_user_id()[1];
			$novaLinha->active = '1';
			
			if ($novaLinha->save()) {
				
				// gerencia notificacao
				if (Input::post('notificacao')) {
					Model_Notificacao_Linha::inscreve($novaLinha->id, $auth->get_user_id()[1]);
				}
				
				// dispara evento
				Event::trigger('notificacao.colonia', array(
														'id_colonia' => $novaLinha->colony_id,
														'id_usuario' => $auth->get_user_id()[1],
														'frase' => 'Uma nova linha foi inserida na colônia "[nome_colonia]"'
													  ));
				
				// gera revisão somente com valores novos
				$revisao = new Model_Revision();
				$revisao->user_id = $auth->get_user_id()[1];
				$revisao->date = \DB::expr('CURRENT_TIMESTAMP');
				$revisao->approved = Model_Revision::REVISAO_APROVADA;
				$revisao->save();
				
				// cadastra dados da revisão
				$dadoRevisao = new Model_Trail_Log();
				$dadoRevisao->trail_id = $novaLinha->id;
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
						
						// cadastra a relação da fonte com a linha
						
						$linhaFonte = new Model_Linha_Fonte();
						$linhaFonte->linha_id = $novaLinha->id;
						$linhaFonte->fonte_id = $fonte->id;
						$linhaFonte->revisao_id = $revisao->id;
						$linhaFonte->pagina = Input::post('fonte_pagina')[$i];
						$linhaFonte->observacao = Input::post('fonte_notas')[$i];
						$linhaFonte->save();
						
					
					}
					
				}
				
				Response::redirect('linhas/listagem?ok');
			}
			
			Response::redirect('linhas/listagem?nok');
			
		} else {
			
			$view = View::forge('linhas/inserir');
			// menu
			$view->set_global('menu', View::forge('dashboard/menu'));
		
			$colonias = DB::query(
				'SELECT 
				 c.name, c.id, c.public
				
				 FROM 
				 colonies c
				
				ORDER BY
				c.name ASC'
			)->as_object('Model_Colony')->execute();
			
			$view->set('colonias', $colonias->as_array());
			
			$this->template->title = 'Linhas &raquo; Inserir Linha';
			$this->template->content = $view;
		}
	}
	
	public function action_notificacoes($idLinha, $acao) {
	
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
	
		if ($acao == 'reativar') {
			Model_Notificacao_Linha::inscreve($idLinha, $idUsuario);
		} else if ($acao == 'desativar') {
			Model_Notificacao_Linha::desinscreve($idLinha, $idUsuario);
		}
	
		// retorna
		Response::redirect('linhas/listagem?ok');
	
	}
	
	public function action_mudar_status() {
		
		$auth = Auth::instance();
		
		// TODO
		// trigger para aplicar alteração sobre linhas e lotes
		
		// carrega linha 
		$linha = Model_Trail::find(Input::get('id'));
		
		// altera coluna que representa estado
		if ($linha->active == '1') {
			$linha->active = '0';
		} else {
			$linha->active = '1';
		}
		
		$linha->last_editor_id = $auth->get_user_id()[1];
		
		
		// salva
		if ($linha->save()) {
				
			// dispara evento
			Event::trigger('notificacao.linha', array(
			'id_linha' => $linha->id,
			'id_usuario' => $auth->get_user_id()[1],
			'frase' => 'A linha "' . $linha->name . '" e seus descendentes foram ' . ($linha->active==1?'reativados':'desativados')
			));
				
			Response::redirect('linhas/listagem?ok');
		} else {
			Response::redirect('linhas/listagem?nok');
		}
	}
		
	public function action_revisoes() {
		
		$view = View::forge('linhas/revisoes');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		// busca dados da linha
		$dadosLinha = Model_Trail::find(Input::get('id'));
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLinha->colony_id);
		
		$dadosLinha->nome_colonia = $colonia->name;
		
		// busca revisões da linha
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
				          trails_log 
				
			    	      WHERE 
				          trail_id = ' . Input::get('id') . '
				         )
				
				ORDER BY
				r.date DESC, r.id DESC'
		)->as_object('Model_Revision')->execute();
		
		if (count($dadosRevisoes)) {
			foreach ($dadosRevisoes as $revisao) {
				
				// busca alterações
				$alteracoesRevisao = DB::query(
					'SELECT
				     attribute, old_value, new_value, cold.name AS colonia_antiga, cnew.name AS colonia_nova
						
					 FROM
				     trails_log t left join 
                     colonies cold on cold.id=t.old_value left join 
                     colonies cnew on cnew.id=t.new_value
						
				     WHERE
				     revision_id = ' . $revisao->id
				)->execute();
				
				$revisao->alteracoes = $alteracoesRevisao->as_array();
				
				// busca fontes
				$fontesRevisao = DB::query(
					'SELECT
					 f.titulo, f.autor, f.editora, lf.pagina, lf.observacao
						
					 FROM
					 linha_fonte lf INNER JOIN
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
		
		$view->set_global('dadosLinha', $dadosLinha);
		$view->set_global('revisoes', $dadosRevisoes->as_array());
		
		$this->template->title = 'Linhas &raquo; Revisões';
		$this->template->content = $view;
		
	}
	
	public function action_reprovar_revisao() {
		
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
			
		
		// restaura valores
		
			// busca valores
			$historico = Model_Trail_Log::query()->where('revision_id', Input::get('id'))->get();
			
			foreach ($historico as $item) {
				// busca colônia
				$linha = Model_Trail::find($item->trail_id);
				$idLinha = $linha->id;
				$nomeLinha = $linha->name;
				// atualiza valor
				$linha->{$item->attribute} = $item->old_value;
				$linha->last_editor_id = $idUsuario;
				$linha->reprovacao = 1;
				$linha->save();
			}
		
		
		// rejeita revisão
		$revisao = Model_Revision::find(Input::get('id'));
		$revisao->approved = '0';
		$revisao->usuario_reprovador_id = $idUsuario;
		$revisao->data_reprovacao = Date::time()->get_timestamp();
		
		if ($revisao->save() && isset($idLinha)) {
			// dispara evento
			Event::trigger('notificacao.linha', array(
												'id_linha' => $idLinha,
												'id_usuario' => $idUsuario,
												'frase' => 'A revisão #' . Input::get('id') . ' da linha "' . $nomeLinha . '" foi rejeitada'
														));
		}
		
		// TODO mensagem de ok/erro	
		// redireciona de volta para listagem
		Response::redirect('linhas/revisoes?id=' . $linha->id);
		
	}
	
	public function action_listagem() {
		
		$view = View::forge('linhas/listagem');
		
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
		
		$where = '';
		if (Input::post()) {
			$nome_linha = Input::post('nome_linha');
			$nome_colonia = Input::post('nome_colonia');
			
			if (!empty($nome_linha)) {
				$where[] = 't.name LIKE \'%'.$nome_linha.'%\'';
			}
			
			if (!empty($nome_colonia)) {
				$where[] = 'c.name LIKE \'%'.$nome_colonia.'%\'';
			}
			
			$where = implode(' AND ', $where);
		}
		
		// usuario logado
		$auth = Auth::instance();
		$idUsuario = $auth->get_user_id()[1];
		
		// busca todas as colônias
		$dadosLinhas = DB::query(
				'SELECT
				 t.id, t.name, t.active, c.name AS nome_colonia, u.username, (SELECT COUNT(DISTINCT revision_id) FROM trails_log WHERE trail_id = t.id GROUP BY trail_id) AS number_of_revisions, nl.id_usuario AS notificacao
		
				 FROM
				 trails t INNER JOIN users u ON t.user_id = u.id INNER JOIN colonies c ON c.id = t.colony_id LEFT JOIN 
				 notificacao_linha nl ON nl.id_linha = t.id AND nl.id_usuario = ' . $idUsuario . '
				
				
				'.($where?'WHERE '.$where:'').'
		
				ORDER BY
				c.name ASC, t.name ASC'
		)->as_object('Model_Trail')->execute();
		
		$view->set_global('linhas', $dadosLinhas->as_array());
		
		$this->template->title = 'Linhas &raquo; Listagem';
		$this->template->content = $view;
	}
	
	public function action_visualizar() {
	
		$view = View::forge('linhas/visualizar');
	
		// menu
		$view->set_global('menu', View::forge('dashboard/menu'));
	
		// busca dados da linha
		$dadosLinha = Model_Trail::find(Input::get('id'));
		
		// busca nome da colônia
		$colonia = Model_Colony::find($dadosLinha->colony_id);
		
		$dadosLinha->nome_colonia = $colonia->name;
		
		// busca as fontes das revisões aprovadas da linha
		$dadosFontes = $dadosLinha->buscaFontes();
		
		$view->set_global('dadosLinha', $dadosLinha);
		$view->set_global('fontes', $dadosFontes);
	
		$this->template->title = 'Linhas &raquo; Visualizar';
		$this->template->content = $view;
	
	}
}

?>