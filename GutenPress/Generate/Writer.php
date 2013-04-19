<?php

namespace GutenPress\Generate;

class Writer{
	private $files = array();
	private $append = array();
	private $dircopy = array();
	private $filecopy = array();
	public function __construct(){

	}
	public function createFile( $location, $contents ){
		$this->files[ $location ] = $contents;
	}
	public function appendToFile( $existing_file, $contents ){

	}
	public function copyDirectory( $source, $destination ){

	}
	public function copyFile( $source, $destination ){

	}
	/**
	 * Write all changes to the filesystem
	 * @return void
	 */
	public function write(){
		foreach ( $this->files as $file => $contents ) {
			$written = file_put_contents( $file , $contents );
			if ( $written === false ) {
				throw new \Exception( sprintf( __('Couldn\'t write to file %1$s', 'gutenpress'), $file ) );
			}
		}
		return true;
	}
	private function writeFiles(){

	}
	private function appendFiles(){

	}
	private function copyDirs(){

	}
	private function copyFiles(){

	}
}