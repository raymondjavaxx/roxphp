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
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Pagination Result
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_ActiveRecord_PaginationResult extends ArrayObject {

	/**
	 * Total of pages
	 * 
	 * @var integer
	 */
	protected $_pages;

	/**
	 * The current page
	 * 
	 * @var integer
	 */
	protected $_currentPage;

	/**
	 * Rox_ActiveRecord_PaginationResult::__construct()
	 * 
	 * @param array $array
	 * @param integer $pages
	 * @param integer $currentPage
	 * @return void
	 */
	public function __construct($array, $pages, $currentPage) {
		parent::__construct($array);
		$this->_pages = $pages;
		$this->_currentPage = $currentPage;
	}

	/**
	 * Returns the total of pages.
	 * 
	 * @return integer
	 */
	public function getPages() {
		return $this->_pages;
	}

	/**
	 * Returns the current page.
	 * 
	 * @return integer
	 */
	public function getCurrentPage() {
		return $this->_currentPage;
	}
}
