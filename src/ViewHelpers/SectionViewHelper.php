<?php
namespace TYPO3\Fluid\ViewHelpers;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\Fluid\Core\ViewHelper\CompilableInterface;
use TYPO3\Fluid\Core\ViewHelper\PostParseInterface;
use TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * A ViewHelper to declare sections in templates for later use with e.g. the RenderViewHelper.
 *
 * = Examples =
 *
 * <code title="Rendering sections">
 * <f:section name="someSection">This is a section. {foo}</f:section>
 * <f:render section="someSection" arguments="{foo: someVariable}" />
 * </code>
 * <output>
 * the content of the section "someSection". The content of the variable {someVariable} will be available in the partial as {foo}
 * </output>
 *
 * <code title="Rendering recursive sections">
 * <f:section name="mySection">
 *  <ul>
 *    <f:for each="{myMenu}" as="menuItem">
 *      <li>
 *        {menuItem.text}
 *        <f:if condition="{menuItem.subItems}">
 *          <f:render section="mySection" arguments="{myMenu: menuItem.subItems}" />
 *        </f:if>
 *      </li>
 *    </f:for>
 *  </ul>
 * </f:section>
 * <f:render section="mySection" arguments="{myMenu: menu}" />
 * </code>
 * <output>
 * <ul>
 *   <li>menu1
 *     <ul>
 *       <li>menu1a</li>
 *       <li>menu1b</li>
 *     </ul>
 *   </li>
 * [...]
 * (depending on the value of {menu})
 * </output>
 *
 * @api
 */
class SectionViewHelper extends AbstractViewHelper implements CompilableInterface {

	/**
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the section', TRUE);
	}

	/**
	 * Save the associated ViewHelper node in a static public class variable.
	 * called directly after the ViewHelper was built.
	 *
	 * @param ViewHelperNode $syntaxTreeNode
	 * @param TextNode[] $viewHelperArguments
	 * @param VariableProviderInterface $variableContainer
	 * @return void
	 */
	static public function postParseEvent(
		ViewHelperNode $syntaxTreeNode,
		array $viewHelperArguments,
		VariableProviderInterface $variableContainer
	) {
		/** @var $nameArgument TextNode */
		$nameArgument = $viewHelperArguments['name'];
		$sectionName = $nameArgument->getText();
		if (!$variableContainer->exists('sections')) {
			$variableContainer->add('sections', array());
		}
		$sections = $variableContainer->get('sections');
		$sections[$sectionName] = $syntaxTreeNode;
		$variableContainer->remove('sections');
		$variableContainer->add('sections', $sections);
	}

	/**
	 * Rendering directly returns all child nodes.
	 *
	 * @return string HTML String of all child nodes.
	 * @api
	 */
	public function render() {
		$content = '';
		if ($this->viewHelperVariableContainer->exists('TYPO3\Fluid\ViewHelpers\SectionViewHelper', 'isCurrentlyRenderingSection')) {
			$this->viewHelperVariableContainer->remove('TYPO3\Fluid\ViewHelpers\SectionViewHelper', 'isCurrentlyRenderingSection');
			$content = $this->renderChildren();
		}
		return $content;
	}

	/**
	 * The inner contents of a section should not be rendered.
	 *
	 * @param string $argumentsName
	 * @param string $closureName
	 * @param string $initializationPhpCode
	 * @param NodeInterface $node
	 * @param TemplateCompiler $compiler
	 * @return string
	 */
	public function compile($argumentsName, $closureName, &$initializationPhpCode, NodeInterface $node, TemplateCompiler $compiler) {
		return '\'\'';
	}
}
