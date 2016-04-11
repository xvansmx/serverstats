<?php

namespace ServerStats\Os\Abstracts;

use ServerStats\Os\Interfaces\InterfaceOs;

abstract class AbstractOs implements InterfaceOs {

	protected $data = [];

	public function __construct () {}

}