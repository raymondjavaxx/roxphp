<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Pagination Helper
 *
 * @package Rox
 */
class Rox_Helper_Pagination {

	/**
	 * Generates the pagination nav
	 * 
	 * @param Rox_ActiveRecord_PaginationResult $collection
	 * @param array $options
	 * @return string
	 */
	public function links(Rox_ActiveRecord_PaginationResult $collection, $options = array()) {
		$defaults = array(
			'class'          => 'pagination',
			'previous_label' => '&laquo; Previous',
			'next_label'     => 'Next &raquo;',
			'max_items'      => 8,
			'link_separator' => ' '
		);

		$options += $defaults;
		$currentPage = $collection->getCurrentPage();

		$output = array();

		if ($collection->getPreviousPage() != $currentPage) {
			$output[] = $this->_link($collection->getPreviousPage(), $options['previous_label'], 'prev');
		}

		$start = max(1, $currentPage - floor($options['max_items'] / 2));
		$end   = min($collection->getPages(), $options['max_items'] + $start - 1);

		if ($start > 1) {
			$output[] = $this->_link(1);
			$output[] = '<span>...</span>';
		}

		for ($i = $start; $i<=$end; $i++) {
			$output[] = ($i == $currentPage) ? "<em>{$currentPage}</em>" : $this->_link($i);
		}

		if ($end < $collection->getPages()) {
			$output[] = '<span>...</span>';
			$output[] = $this->_link($collection->getPages());
		}

		if ($collection->getNextPage() != $currentPage) {
			$output[] = $this->_link($collection->getNextPage(), $options['next_label'], 'next');
		}

		$output = implode($options['link_separator'], $output);
		return sprintf('<div class="%s">%s</div>', $options['class'], $output);
	}

	/**
	 * Generates a pagination link
	 *
	 * @param integer $page 
	 * @param string $text 
	 * @param string $rel
	 * @return string
	 */
	protected function _link($page, $text = false, $rel = false) {
		if ($text === false) {
			$text = (string)$page;
		}

		$attributes = ($rel === false) ? '' : ' rel="' . $rel . '"';

		$vars = array_merge($_GET, array('page' => $page));
		unset($vars['route']);
		$href = '?' . htmlspecialchars(http_build_query($vars));

		return sprintf('<a href="%s"%s>%s</a>', $href, $attributes, $text);
	}
}
