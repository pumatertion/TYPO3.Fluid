<?php
namespace TYPO3\Fluid\Tests\Unit\Core\ViewHelper;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\Fluid\Core\Parser\ParsingState;
use TYPO3\Fluid\Core\Parser\SyntaxTree\ArrayNode;
use TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\Fluid\Core\Rendering\RenderingContext;
use TYPO3\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\Fluid\Tests\UnitTestCase;

/**
 * Class ViewHelperInvokerTest
 */
class ViewHelperInvokerTest extends UnitTestCase {

	public function testInvokeViewHelper() {
		$resolver = new ViewHelperResolver();
		$invoker = new ViewHelperInvoker($resolver);
		$renderingContext = new RenderingContext();
		$node = new ViewHelperNode($resolver, 'f', 'count', array('subject' => new ArrayNode(array(1))), new ParsingState());
		$result = $invoker->invoke($node, $renderingContext);
		$this->assertEquals(1, $result);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\Fluid\Core\ViewHelper\Exception
	 */
	public function abortIfUnregisteredArgumentsExistThrowsExceptionOnUnregisteredArguments() {
		$expected = array(new ArgumentDefinition('firstArgument', 'string', '', FALSE));
		$actual = array('firstArgument' => 'foo', 'secondArgument' => 'bar');

		$templateParser = $this->getAccessibleMock('TYPO3\Fluid\Core\ViewHelper\ViewHelperInvoker', array('dummy'), array(), '', FALSE);

		$templateParser->_call('abortIfUnregisteredArgumentsExist', $expected, $actual);
	}

	/**
	 * @test
	 */
	public function abortIfUnregisteredArgumentsExistDoesNotThrowExceptionIfEverythingIsOk() {
		$expectedArguments = array(
			new ArgumentDefinition('name1', 'string', 'desc', FALSE),
			new ArgumentDefinition('name2', 'string', 'desc', TRUE)
		);
		$actualArguments = array(
			'name1' => 'bla'
		);

		$mockTemplateParser = $this->getAccessibleMock('TYPO3\Fluid\Core\ViewHelper\ViewHelperInvoker', array('dummy'), array(), '', FALSE);

		$mockTemplateParser->_call('abortIfUnregisteredArgumentsExist', $expectedArguments, $actualArguments);
		// dummy assertion to avoid "did not perform any assertions" error
		$this->assertTrue(TRUE);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\Fluid\Core\ViewHelper\Exception
	 */
	public function abortIfRequiredArgumentsAreMissingThrowsException() {
		$expected = array(
			new ArgumentDefinition('firstArgument', 'string', '', FALSE),
			new ArgumentDefinition('secondArgument', 'string', '', TRUE)
		);

		$templateParser = $this->getAccessibleMock('TYPO3\Fluid\Core\ViewHelper\ViewHelperInvoker', array('dummy'), array(), '', FALSE);

		$templateParser->_call('abortIfRequiredArgumentsAreMissing', $expected, array());
	}

	/**
	 * @test
	 */
	public function abortIfRequiredArgumentsAreMissingDoesNotThrowExceptionIfRequiredArgumentExists() {
		$expectedArguments = array(
			new ArgumentDefinition('name1', 'string', 'desc', FALSE),
			new ArgumentDefinition('name2', 'string', 'desc', TRUE)
		);
		$actualArguments = array(
			'name2' => 'bla'
		);

		$mockTemplateParser = $this->getAccessibleMock('TYPO3\Fluid\Core\ViewHelper\ViewHelperInvoker', array('dummy'), array(), '', FALSE);

		$mockTemplateParser->_call('abortIfRequiredArgumentsAreMissing', $expectedArguments, $actualArguments);
		// dummy assertion to avoid "did not perform any assertions" error
		$this->assertTrue(TRUE);
	}

}
