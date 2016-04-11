<?php

namespace ServerStats\Os;

use ServerStats\Os\Interfaces\InterfaceOs;

class Factory {

	// Contenedor de servicios
	private $services = [];

	// Contenedor de datos
	private $data = [];

	public function load (Array $namespaces) {

		// Conteo de servicios
		$count_namespaces = count($namespaces);

		// Se registran los servicios
		for ($i = 0; $i < $count_namespaces; $i++) {

			// Se valida si existe el namespace
			if (class_exists($namespaces[$i])) {
				// Se agrega al contenedor
				$service = new $namespaces[$i];

				// Revisamos el contrato
				if ($service instanceof InterfaceOs)
					$this->services[$namespaces[$i]] = $service;

			} else {
				// En caso de no existir
				throw new Exception("Service {$namespaces[$i]} does not exists.");
			}
		}

	}

	public function excecute () {

		foreach ($this->services as $key => $service) {
			$service->excecute();
		}

	}

	public function loadedServices () {

		// Devuelve todos los namespaces del contenedor
		return array_keys($this->services);
	}

}