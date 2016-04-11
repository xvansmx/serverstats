<?php

namespace ServerStats\Os\Darwin;

use ServerStats\Os\Abstracts\AbstractOs;
use ServerStats\Os\Interfaces\InterfaceOs;

class Darwin extends AbstractOs {

	public function hdd () {

		$data = [];

		$hdd_total_space = disk_total_space('/');
		$hdd_total_space = intval($hdd_total_space) / 1024 / 1024 / 1024;

		$hdd_free_space = disk_free_space('/');
		$hdd_free_space = intval($hdd_free_space) / 1024 / 1024 / 1024;

		$data['space_free'] = number_format($hdd_free_space, 2, '.', '');
		$data['space_total'] = number_format($hdd_total_space, 2, '.', '');
		$data['space_used'] = floatval($data['space_total']) - floatval($data['space_free']);

		return $data;
	}

	public function uptime () {

		// Se ejecuta el comando
		$result = shell_exec("sysctl -n kern.boottime");

		$daat = [];

		// Format: { sec = 1460391183, usec = 0 } Mon Apr 11 11:13:03 2016
		$temp_data = explode(" ", $result);
		if ($temp_data[0] == '{') {
			$microtime = trim($temp_data[3], ",");
			$data['uptime'] = time() - $microtime;
		}

		return $data['uptime'];
	}

	public function process () {
		
		$result = shell_exec("ps aux");
		$list = explode("\n", $result);

		unset($list[0]);
		$procs = 0.0;

		foreach ($list as $index => $line) {
			$reg = preg_replace('/\s\s+/', ' ', $line);
			$exp = explode(' ', $reg);
			if (isset($exp[2]))
				$procs += floatval($exp[2]);
		}

		return $procs;
	}

	public function processes () {
		
		$result = shell_exec("top -l 1 -s 0 | awk '/Processes/'");
		
		$result = preg_replace('/Processes: /', '', $result);
		$result = preg_replace('/( total, | running, | stuck, | sleeping, | threads)/', ',', $result);

		$exp = explode(',', $result);

		$data = [];
		$data['total'] 		= $exp[0];
		$data['running'] 	= $exp[1];
		$data['stuck'] 		= $exp[2];
		$data['sleeping'] 	= $exp[3];
		$data['threads'] 	= $exp[4];

		return $data;
	}

	public function memory () {

		// Datos temporales
		$temp_data = [];

		$result = shell_exec("sysctl hw.memsize");

		$result = preg_replace('/hw.memsize: /', '', $result);
		$result = preg_replace('/hw.memsize = /', '', $result);

		$temp_data['memory_total'] = $result;

		$result = null;

		// Ejecutamod el comando
		$result = shell_exec("vm_stat");

		preg_match('/^Pages free:\s+(\S+)/m', $result, $temp_data['pages_free']);
		preg_match('/^Anonymous pages:\s+(\S+)/m', $result, $temp_data['anonymous_pages']);
		preg_match('/^Pages wired down:\s+(\S+)/m', $result, $temp_data['pages_wired_down']);
		preg_match('/^File-backed pages:\s+(\S+)/m', $result, $temp_data['file_backed_pages']);
		preg_match('/^Pages occupied by compressor:\s+(\S+)/m', $result, $temp_data['pages_occupied_by_compressor']);
		preg_match('/^Pages speculative:\s+(\S+)/m', $result, $temp_data['pages_speculative']);
		preg_match('/^Pages wired down:\s+(\S+)/m', $result, $temp_data['pages_wired_down']);
		preg_match('/^Pages active:\s+(\S+)/m', $result, $temp_data['pages_active']);

		// Matriz para devolucion
		$data = [];

		$data['memory_total'] = intval($temp_data['memory_total']);
		$data['memory_free'] = intval($temp_data['pages_free'][1]) * 4 * 1024;
		$data['memory_application'] = (intval($temp_data['anonymous_pages'][1]) + intval($temp_data['anonymous_pages'][1])) * 4 * 1024;
		$data['memory_cache'] = intval($temp_data['file_backed_pages'][1]) * 4 * 1024;
		$data['memory_buffer'] = intval($temp_data['pages_occupied_by_compressor'][1]) * 4 * 1024;
		$data['memory_used'] = ( intval($data['memory_total']) - intval($data['memory_free']) );

		return $data;
	}

	public function excecute () {
		$this->data['server']['uptime'] = $this->uptime();
		$this->data['server']['process'] = $this->process();
		$this->data['server']['processes'] = $this->processes();
		$this->data['memory'] = $this->memory();
		$this->data['hdd'] = $this->hdd();

		print_r($this->data);
	}

}