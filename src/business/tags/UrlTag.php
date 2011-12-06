<?php

class UrlTag extends Tag
{
	private $_segments;

	/**
	 * Base
	 */
	public function base()
	{
		return Blocks::app()->urlManager->getBaseUrl();
	}

	/**
	 * Get Segments
	 * @return array The URL segments
	 * @access private
	 */
	private function _getSegments()
	{
		if (!isset($this->_segments))
			$this->_segments = Blocks::app()->request->getPathSegments();

		return $this->_segments;
	}

	/**
	 * Segments
	 */
	public function segments()
	{
		return $this->_getSegments();
	}

	/**
	 * Segment
	 * @param int $num Which segment to retrieve
	 */
	public function segment($num)
	{
		$segments = $this->_getSegments();
		$index = $num - 1;

		if (isset($segments[$index]))
			return $segments[$index];

		return false;
	}

	public function domain()
	{
		return Blocks::app()->request->getServerName();
	}

	public function controllerUrl($controller, $action)
	{
		return BlocksHtml::controllerUrl($controller, $action);
	}
}
