<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Pagination Helper
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Helper_Pagination {

	protected $_options = array(
		'class'          => 'pagination',
		'previous_label' => '&laquo; Previous',
		'next_label'     => 'Next &raquo;'
	);

	/**
	 * undocumented function
	 * 
	 * @param Rox_ActiveRecord_PaginationResult $collection
	 * @return string
	 */
	public function links(Rox_ActiveRecord_PaginationResult $collection, $options = array()) {
		$options = array_merge($this->_options, $options);
		$currentPage = $collection->getCurrentPage();

		$output = array();

		if ($collection->getPreviousPage() != $currentPage) {
			$output[] = $this->_linkOrSpan($collection->getPreviousPage(), $currentPage,
				$options['previous_label']);
		}

		for ($i =1; $i<=$collection->getPages(); $i++) {
			$output[] = $this->_linkOrSpan($i, $currentPage);
		}

		if ($collection->getNextPage() != $currentPage) {
			$output[] = $this->_linkOrSpan($collection->getNextPage(), $currentPage,
				$options['next_label']);
		}

		return sprintf('<div class="%s">%s</div>', $options['class'], implode(' ', $output));
	}

	/**
	 * undocumented function
	 *
	 * @param integer $page 
	 * @param integer $currentPage 
	 * @param string $text 
	 * @return string
	 */
	protected function _linkOrSpan($page, $currentPage, $text = null) {
		if (null == $text) {
			$text = (string)$page;
		}

		if ($page == $currentPage) {
			return sprintf('<span class="current">%s</span>', $text);
		}

		$getVars = $_GET;
		unset($getVars['route']);
		$query = http_build_query(array_merge($getVars, array('page' => $page)));
		return sprintf('<a href="?%s">%s</a>', htmlspecialchars($query), $text);
	}
}
