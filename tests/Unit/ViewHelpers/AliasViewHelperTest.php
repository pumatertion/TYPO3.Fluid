<?php
namespace TYPO3\Fluid\Tests\Unit\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\Fluid\ViewHelpers\AliasViewHelper;

/**
 * Testcase for AliasViewHelper
 */
class AliasViewHelperTest extends ViewHelperBaseTestcase {

	/**
	 * @test
	 */
	public function testInitializeArgumentsRegistersExpectedArguments() {
		$instance = $this->getMock('TYPO3\\Fluid\\ViewHelpers\\AliasViewHelper', array('registerArgument'));
		$instance->expects($this->at(0))->method('registerArgument')->with('map', 'array', $this->anything(), TRUE);
		$instance->initializeArguments();
	}

	/**
	 * @test
	 */
	public function renderAddsSingleValueToTemplateVariableContainerAndRemovesItAfterRendering() {
		$viewHelper = new AliasViewHelper();

		$mockViewHelperNode = $this->getMock(
			'TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode',
			array('evaluateChildNodes'),
			array(), '', FALSE
		);
		$mockViewHelperNode->expects($this->once())->method('evaluateChildNodes')->will($this->returnValue('foo'));

		$this->injectDependenciesIntoViewHelper($viewHelper);
		$viewHelper->setViewHelperNode($mockViewHelperNode);
		$viewHelper->setArguments(array('map' => array('someAlias' => 'someValue')));
		$viewHelper->render();
	}

	/**
	 * @test
	 */
	public function renderAddsMultipleValuesToTemplateVariableContainerAndRemovesThemAfterRendering() {
		$viewHelper = new AliasViewHelper();

		$mockViewHelperNode = $this->getMock(
			'TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode',
			array('evaluateChildNodes'),
			array(), '', FALSE
		);
		$mockViewHelperNode->expects($this->once())->method('evaluateChildNodes')->will($this->returnValue('foo'));

		$this->injectDependenciesIntoViewHelper($viewHelper);
		$viewHelper->setViewHelperNode($mockViewHelperNode);
		$viewHelper->setArguments(array('map' => array('someAlias' => 'someValue', 'someOtherAlias' => 'someOtherValue')));
		$viewHelper->render();
	}

	/**
	 * @test
	 */
	public function renderDoesNotTouchTemplateVariableContainerAndReturnsChildNodesIfMapIsEmpty() {
		$viewHelper = new AliasViewHelper();

		$mockViewHelperNode = $this->getMock(
			'TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode',
			array('evaluateChildNodes'), array(), '',
			FALSE
		);
		$mockViewHelperNode->expects($this->once())->method('evaluateChildNodes')->will($this->returnValue('foo'));

		$this->injectDependenciesIntoViewHelper($viewHelper);
		$viewHelper->setViewHelperNode($mockViewHelperNode);

		$viewHelper->setArguments(array('map' => array()));
		$this->assertEquals('foo', $viewHelper->render());
	}
}
