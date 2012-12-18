<?php

namespace GutenPress\Model;

abstract class PostMeta{
	protected $data_model;
	abstract protected static function setDataModel();
	abstract public function init();
}