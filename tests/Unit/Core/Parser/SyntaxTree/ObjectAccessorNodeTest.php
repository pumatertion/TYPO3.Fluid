<?php
namespace TYPO3\Fluid\Tests\Unit\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3\Fluid\Core\Parser\SyntaxTree\ObjectAccessorNode;
use TYPO3\Fluid\Tests\Unit\Core\Parser\SyntaxTree\Fixtures\ObjectHavingGetter;
use TYPO3\Fluid\Tests\UnitTestCase;

/**
 * Testcase for ObjectAccessorNode
 */
class ObjectAccessorNodeTest extends UnitTestCase {

	/**
	 * @test
	 * @dataProvider getEvaluateTestValues
	 * @param array $variables
	 * @param string $path
	 * @param mixed $expected
	 */
	public function testEvaluateGetsExpectedValue(array $variables, $path, $expected) {
		$node = new ObjectAccessorNode($path);
		$renderingContext = $this->getMock('TYPO3\Fluid\Core\Rendering\RenderingContextInterface');
		$variableContainer = new StandardVariableProvider($variables);
		$renderingContext->expects($this->any())->method('getVariableProvider')->will($this->returnValue($variableContainer));
		$value = $node->evaluate($renderingContext);
		$this->assertEquals($expected, $value);
	}

	/**
	 * @return array
	 */
	public function getEvaluateTestValues() {
		return array(
			array(array(), 'on', TRUE),
			array(array(), 'yes', TRUE),
			array(array(), 'true', TRUE),
			array(array(), 'off', FALSE),
			array(array(), 'no', FALSE),
			array(array(), 'false', FALSE),
			array(array('foo' => 'bar'), '_all', array('foo' => 'bar')),
			array(array('foo' => 'bar'), 'foo', 'bar'),
			array(array('foo' => array('bar' => 'test')), 'foo.bar', 'test'),
			array(array('foo' => array('bar' => 'test'), 'dynamic' => 'bar'), 'foo.{dynamic}', 'test'),
			array(array('foo' => array('bar' => 'test'), 'dynamic' => 'bar'), 'foo.{dynamic}', 'test'),
			array(array('foo' => new ObjectHavingGetter()), 'foo.accessible', 'accessible'),
			array(array('foo' => new ObjectHavingGetter()), 'foo.notAccessible1', NULL),
			array(array('foo' => new ObjectHavingGetter()), 'foo.notAccessible2', NULL)
		);
	}

}
