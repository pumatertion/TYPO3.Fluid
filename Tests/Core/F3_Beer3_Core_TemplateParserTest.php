<?php
declare(ENCODING = 'utf-8');
namespace F3::Beer3::Core;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package Beer3
 * @subpackage Tests
 * @version $Id:$
 */
/**
 * Testcase for TemplateParser
 *
 * @package Beer3
 * @subpackage Tests
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class TemplateParserTest extends F3::Testing::BaseTestCase {

	/**
	 * @var F3::Beer3::TemplateParser
	 */
	protected $templateParser;

	/**
	 * Sets up this test case
	 *
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function setUp() {
		$this->templateParser = new F3::Beer3::Core::TemplateParser();
		$this->templateParser->injectComponentFactory($this->componentFactory);
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.or>
	 * @expectedException F3::Beer3::ParsingException
	 */
	public function parseThrowsExceptionWhenStringArgumentMissing() {
		$this->templateParser->parse(123);
	}

	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function parseExtractsNamespacesCorrectly() {
		$this->templateParser->parse("{namespace f3=F3::Beer3::Blablubb} \{namespace f4=F7::Rocks} {namespace f4=F3::Rocks}");
		$expected = array(
			'f3' => 'F3::Beer3::Blablubb',
			'f4' => 'F3::Rocks'
		);
		$this->assertEquals($this->templateParser->getNamespaces(), $expected, 'Namespaces do not match.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @expectedException F3::Beer3::ParsingException
	 */
	public function parseThrowsExceptionIfNamespaceIsRedeclared() {
		$this->templateParser->parse("{namespace f3=F3::Beer3::Blablubb} {namespace f3=F3::Rocks}");
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture01ReturnsCorrectObjectTree() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture01.html', FILE_TEXT);
		
		$rootNode = new F3::Beer3::Core::SyntaxTree::RootNode();
		$rootNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode("\na"));
		$dynamicNode = new F3::Beer3::Core::SyntaxTree::ViewHelperNode('F3::Beer3::ViewHelpers', 'base', new F3::Beer3::ViewHelpers::BaseViewHelper(), array());
		$rootNode->addChildNode($dynamicNode);
		$rootNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode('b'));
		
		$expected = $rootNode;
		$actual = $this->templateParser->parse($templateSource);
		$this->assertEquals($expected, $actual, 'Fixture 01 was not parsed correctly.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture02ReturnsCorrectObjectTree() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture02.html', FILE_TEXT);
		
		$rootNode = new F3::Beer3::Core::SyntaxTree::RootNode();
		$rootNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode("\n"));
		$dynamicNode = new F3::Beer3::Core::SyntaxTree::ViewHelperNode('F3::Beer3::ViewHelpers', 'base', new F3::Beer3::ViewHelpers::BaseViewHelper, array());
		$dynamicNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode("\nHallo\n"));
		$rootNode->addChildNode($dynamicNode);
		$dynamicNode = new F3::Beer3::Core::SyntaxTree::ViewHelperNode('F3::Beer3::ViewHelpers', 'base', new F3::Beer3::ViewHelpers::BaseViewHelper, array());
		$dynamicNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode("Second"));
		$rootNode->addChildNode($dynamicNode);
		
		$expected = $rootNode;
		$actual = $this->templateParser->parse($templateSource);
		$this->assertEquals($expected, $actual, 'Fixture 02 was not parsed correctly.');
	}
	
	/**
	 * @test
	 * @expectedException F3::Beer3::ParsingException
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture03ThrowsExceptionBecauseWrongTagNesting() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture03.html', FILE_TEXT);
		$this->templateParser->parse($templateSource);
	}
	
	/**
	 * @test
	 * @expectedException F3::Beer3::ParsingException
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture04ThrowsExceptionBecauseClosingATagWhichWasNeverOpened() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture04.html', FILE_TEXT);
		$this->templateParser->parse($templateSource);
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture05ReturnsCorrectObjectTree() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture05.html', FILE_TEXT);
		
		$rootNode = new F3::Beer3::Core::SyntaxTree::RootNode();
		$rootNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode("\na"));
		$dynamicNode = new F3::Beer3::Core::SyntaxTree::ObjectAccessorNode('posts.bla.Testing3');
		$rootNode->addChildNode($dynamicNode);
		$rootNode->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode('b'));
		
		$expected = $rootNode;
		$actual = $this->templateParser->parse($templateSource);
		$this->assertEquals($expected, $actual, 'Fixture 05 was not parsed correctly.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture06ReturnsCorrectObjectTree() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture06.html', FILE_TEXT);
		
		$rootNode = new F3::Beer3::Core::SyntaxTree::RootNode();
		$arguments = array(
			'each' => new F3::Beer3::Core::SyntaxTree::RootNode(),
			'as' => new F3::Beer3::Core::SyntaxTree::RootNode()
		);
		$arguments['each']->addChildNode(new F3::Beer3::Core::SyntaxTree::ObjectAccessorNode('posts'));
		$arguments['as']->addChildNode(new F3::Beer3::Core::SyntaxTree::TextNode('post'));
		$dynamicNode = new F3::Beer3::Core::SyntaxTree::ViewHelperNode('F3::Beer3::ViewHelpers', 'default.for', new F3::Beer3::ViewHelpers::ForViewHelper(), $arguments);
		$rootNode->addChildNode($dynamicNode);
		$dynamicNode->addChildNode(new F3::Beer3::Core::SyntaxTree::ObjectAccessorNode('post'));
		
		$expected = $rootNode;
		$actual = $this->templateParser->parse($templateSource);
		$this->assertEquals($expected, $actual, 'Fixture 06 was not parsed correctly.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture07ReturnsCorrectlyRenderedResult() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture07.html', FILE_TEXT);
		
		$templateTree = $this->templateParser->parse($templateSource);
		$context = new F3::Beer3::VariableContainer(array('id' => 1));
		$result = $templateTree->render($context);
		$expected = '1';
		$this->assertEquals($expected, $result, 'Fixture 07 was not parsed correctly.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture08ReturnsCorrectlyRenderedResult() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture08.html', FILE_TEXT);
		
		$templateTree = $this->templateParser->parse($templateSource);
		$context = new F3::Beer3::VariableContainer(array('idList' => array(0, 1, 2, 3, 4, 5)));
		$result = $templateTree->render($context);
		$expected = '0 1 2 3 4 5 ';
		$this->assertEquals($expected, $result, 'Fixture 08 was not rendered correctly.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture09ReturnsCorrectlyRenderedResult() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture09.html', FILE_TEXT);
		
		$templateTree = $this->templateParser->parse($templateSource);
		$context = new F3::Beer3::VariableContainer(array('idList' => array(0, 1, 2, 3, 4, 5), 'variableName' => 3));
		$result = $templateTree->render($context);
		$expected = '0 hallo test 3 4 ';
		$this->assertEquals($expected, $result, 'Fixture 09 was not rendered correctly. This is most likely due to problems in the array parser.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture10ReturnsCorrectlyRenderedResult() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture10.html', FILE_TEXT);
		
		$templateTree = $this->templateParser->parse($templateSource);
		$context = new F3::Beer3::VariableContainer(array('idList' => array(0, 1, 2, 3, 4, 5)));
		$result = $templateTree->render($context);
		$expected = '0 1 2 3 4 5 ';
		$this->assertEquals($expected, $result, 'Fixture 10 was not rendered correctly. This has proboably something to do with line breaks inside tags.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function fixture11ReturnsCorrectlyRenderedResult() {
		$templateSource = file_get_contents(__DIR__ . '/../Fixtures/TemplateParserTestFixture11.html', FILE_TEXT);
		
		$templateTree = $this->templateParser->parse($templateSource);
		$context = new F3::Beer3::VariableContainer(array());
		$result = $templateTree->render($context);
		$expected = '0 2 4 ';
		$this->assertEquals($expected, $result, 'Fixture 11 was not rendered correctly. This has proboably something to do with line breaks inside tags.');
	}
}



?>