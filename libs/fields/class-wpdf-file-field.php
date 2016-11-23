<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_FileField extends WPDF_FormField {

	protected $_post_max_size = null;
	protected $_upload_max_filesize = null;
	protected $_limit = 0;

	/**
	 * User set limit from field panel
	 * @var int
	 */
	protected $_maxFileSize = null;
	/**
	 * Cvs of allowed file extensions
	 * @var null
	 */
	protected $_allowedExt = null;

	public function __construct( $name, $type, $args = array() ) {
		parent::__construct( $name, $type, $args );

		// find upload limits
		$this->_post_max_size = ini_get('post_max_size');
		$this->_upload_max_filesize = ini_get('upload_max_filesize');
		$this->_limit = $this->_post_max_size;
		if($this->_limit > $this->_upload_max_filesize){
			$this->_limit = $this->_upload_max_filesize;
		}

		$this->_maxFileSize = isset($args['max_file_size']) ? $args['max_file_size'] : $this->_limit;
		$this->_allowedExt = isset($args['allowed_ext']) ? $args['allowed_ext'] : 'jpg,jpeg,png';

	}

	public function getServerLimit(){
		return $this->_limit;
	}

	public function getPostMaxSize(){
		return $this->_post_max_size;
	}

	public function getUploadMaxFilesize(){
		return $this->_upload_max_filesize;
	}

	public function getMaxFileSize(){
		return $this->_maxFileSize;
	}

	public function getAllowedExt() {
		return $this->_allowedExt;
	}

	public function isValidExt($filedata){

		if(empty($this->getAllowedExt())){
			return true;
		}

		$name = $filedata['name'];
		$extensions = explode(',',$this->getAllowedExt());
		$lastPos = strrpos($name, '.');
		$ext = substr($name, $lastPos + 1);
		if(in_array($ext, $extensions)){
			return true;
		}

		return false;
	}

	public function isAllowedSize($filedata){

		if(isset($filedata['size']) && intval($filedata['size']) < $this->getMaxFileSize() * 1024 * 1024){
			return true;
		}
		return false;
	}

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->get($this->_name);

		// display name of previously uploaded file and show the file uploader to allow users to overwrite upload
		echo '<input type="'.$this->getType().'" name="'.$this->getInputName().'" />';
		if(!empty($value)) {
			echo '<input type="hidden" name="' . $this->getInputName() . '_uploaded" value="' . $value . '" />';
			echo sprintf('<p class="wpdf-upload">Uploaded File: <span class="wpdf-upload__name">%s</span></p>', $value);
		}
	}
}