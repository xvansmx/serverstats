<?php

namespace ServerStats\Os\Interfaces;

interface InterfaceOs {

	public function uptime();
	
	public function hdd();

	public function processes();
	
	public function process();

	public function memory();

	public function excecute();

}