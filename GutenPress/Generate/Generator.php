<?php

namespace GutenPress\Generate;

abstract class Generator{

	private $writer;
	private $base_path;
	protected $template;
	protected $generated;
	protected $target_path;
	protected $template_path;
	protected $template_vars;

	public function __construct(){
		// setup paths
		$this->base_path = __DIR__;
		$this->setTargetPath();

		// get a writer object
		$this->writer = new Writer;
	}

	/**
	 * Define the template path for the given generator
	 * @return void
	 */
	protected function getTemplatePath( $template = '' ){
		if ( empty($template) ) {
			$classname = get_class($this);
			$template  = end( explode('\\', $classname) );
		}
		return __DIR__ .'/Templates/'. $template .'.tpl';
	}

	/**
	 * Define the absolute path to the target file(s)
	 * @return string
	 */
	abstract protected function setTargetPath();

	protected function readTemplate( $template_path = '' ){
		if ( empty($template_path) ) {
			$template_path = $this->getTemplatePath();
		}
		$contents = file_get_contents( $template_path );
		if ( $contents === false ) {
			throw new \Exception( __( sprintf('Can\'t read template file at %1$s', $template_path ), 'gutenpress' ) );
		}
		return $contents;
	}

	abstract protected function setTemplateVars();

	public function parseTemplate( $template, $vars, $target ){
		$this->generated[ $target ] = vsprintf( $template, $vars );
	}

	abstract protected function prepareCommit();

	/**
	 * Write changes to filesystem
	 */
	public function commit(){
		// check if the target path exists or try to create it
		if ( ! file_exists($this->target_path) ) {
			$mkdir = mkdir( $this->target_path, 0777, true );
			if ( ! $mkdir ) {
				throw new \Exception( __( sprintf('Failed to create directory at %1$s', $this->target_path), 'gutenpress' ) );
			}
		}

		$this->setTemplateVars();
		$this->prepareCommit();

		// create several files, perhaps?
		foreach ( $this->generated as $path => $contents ) {
			$this->writer->createFile( $path, $contents );
		}

		return $this->writer->write();
	}
}