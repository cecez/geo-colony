<?php

class Qgram {

	const k = 3;
	const q = 3;

	static private function _qgrams($str)
	{
		$ret = array();
		$str = '##'.trim($str).'$$';
		for ($i = 0; $i < mb_strlen($str) - (Qgram::q - 1); $i++)
		{
			$ret[] = array(
				'position' => $i + 1,
				'qgram'    => mb_substr($str, $i, Qgram::q)
			);
		}
		return $ret;
	}

	static public function generate($table, $id)
	{
		Qgram::delete($table, $id);

		$lines = 0;
		$query = 'INSERT INTO `'.$table.'_qgram` (`id`, `position`, `qgram`) VALUES ';

		foreach (Qgram::_generate_select($table, $id) as $data)
		{
			$id = $data['id'];
			$lines++;
			foreach (Qgram::_qgrams($data['name']) as $item)
			{
				$query .= ' ('.$id.', '.$item['position'].', '.DB::quote($item['qgram']).'), ';
			}
		}
		DB::query($query)->execute();
		echo $lines.' lines inserted';
	}

	static private function _generate_select($table, $id)
	{
		$sel = DB::select('id', 'name')->from($table);
		if ($id != '*')
		{
			$sel = $sel->where('id', $id);
		}
		return $sel->execute();
	}

	static public function delete($table, $id)
	{
		$del = DB::delete($table.'_qgram');
		if ($id != '*')
		{
			$del = $del->where('id', $id);
		}
		$del->execute();
	}



	static public function search($table, $str, $join_table = '')
	{
		DB::query('CREATE TEMPORARY TABLE `qgram` (
			`position` smallint(5), `qgram` char(3)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;')->execute();

		$sql = 'INSERT INTO `qgram` (`position`, `qgram`)';		
		foreach (Qgram::_qgrams($str) as $item)
		{
			DB::query($sql.' VALUES('.$item['position'].', '.DB::quote($item['qgram']).');')->execute();
		}

		$search = DB::quote($str);
		$join_table = $join_table ? 'JOIN '.$join_table : '';
		return DB::query('
			SELECT t.id, t.landholder_name AS name FROM '.$table.' AS t '.$join_table.' WHERE '.Qgram::search_likes($str).'
			UNION (
				SELECT t.id, t.landholder_name
				FROM '.$table.' AS t '.$join_table.', landholders_qgram AS tq, qgram AS q
				WHERE 
					t.id = tq.id AND 
					tq.qgram = q.qgram AND
					ABS(tq.position - q.position) <= '.Qgram::k.' AND 
					ABS(LENGTH(t.landholder_name) - LENGTH('.$search.')) <= '.Qgram::k.' AND
					t.active = 1
				GROUP BY t.id, t.landholder_name
				HAVING
					COUNT(*) >= LENGTH(t.landholder_name) - 1 - ('.Qgram::k.' - 1) * '.Qgram::q.' AND 
					COUNT(*) >= LENGTH('.$search.') - 1 - ('.Qgram::k.' - 1) * '.Qgram::q.'
				ORDER BY edit_distance(t.landholder_name, '.$search.')
			)
		')->execute();
	}

	static private function search_likes($str)
	{
		$array = preg_split('/[\s]+/', $str);
		ArrayUtil::combination($array, $likes);

		$likes = array_map(function($item) { 
			return 't.active = 1 AND t.landholder_name LIKE '.DB::quote('%'.implode('%', $item).'%');
		}, $likes);

		return implode($likes, ' OR ');
	}
}