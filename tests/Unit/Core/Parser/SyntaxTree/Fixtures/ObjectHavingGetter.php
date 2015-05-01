<?php

namespace TYPO3\Fluid\Tests\Unit\Core\Parser\SyntaxTree\Fixtures;

/**
 * Class ObjectHavingGetter
 *
 * @package TYPO3\Fluid\Tests\Unit\Core\Parser\SyntaxTree\Fixtures
 */
class ObjectHavingGetter {

	/**
	 * @return string
	 */
	public function getAccessible(){
		return 'accessible';
	}

	/**
	 * @return string
	 */
	protected function getNotAccessible1(){
		return 'notAccessible1';
	}

	/**
	 * @return string
	 */
	private function getNotAccessible2(){
		return 'notAccessible2';
	}

}
