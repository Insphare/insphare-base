<?php
namespace Insphare\Base;

/**
 * Class ImageInfo
 *
 * @package Insphare\Base
 */
class ImageInfo {

	/**
	 * @var int
	 */
	private $width = 0;

	/**
	 * @var int
	 */
	private $height = 0;

	/**
	 * @var null
	 */
	private $type = null;

	/**
	 * @var string
	 */
	private $htmlAttributes = '';

	/**
	 * @var string
	 */
	private $mime = '';

	/**
	 * @var int
	 */
	private $colorBits = 0;

	/**
	 * @var string
	 */
	private $filePath = '';

	/**
	 * @param $file
	 */
	public function __construct($file) {

		$this->filePath = $file;

		if (!file_exists($file)) {
			throw new \InvalidArgumentException($file . ' does not exist.');
		}

		if (!is_readable($file)) {
			throw new \InvalidArgumentException($file . ' is not readable.');
		}

		$data = getimagesize($file);
		list($width, $height, $type, $htmlAttributes) = $data;
		$this->width = $width;
		$this->height = $height;
		$this->type = $type;
		$this->htmlAttributes = $htmlAttributes;
		$this->colorBits = isset($data['bits']) ? $data['bits'] : null;
		$this->mime =  isset($data['mime']) ? $data['mime'] : null;;
	}

	/**
	 * @return int
	 */
	public function getColorBits() {
		return $this->colorBits;
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @return null
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getHtmlAttributes() {
		return $this->htmlAttributes;
	}

	/**
	 * @return string
	 */
	public function getMime() {
		return $this->mime;
	}

	/**
	 * Typ of IMG_GIF or IMG_PNG or IMG_GIF
	 * @param int $type
	 * @return bool
	 */
	public function isImageType($type) {
		return $type === $this->type;
	}

	/**
	 * @return bool
	 */
	public function isPng() {
		return $this->type === IMG_PNG;
	}

	/**
	 * @return bool
	 */
	public function isGif() {
		return $this->type === IMG_GIF;
	}

	/**
	 * @return bool
	 */
	public function isJpeg() {
		return $this->type === IMG_JPG || $this->type === IMG_JPEG;
	}

	public function getFilePath() {
		return $this->filePath;
	}

}
