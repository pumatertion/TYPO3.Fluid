<?php
namespace TYPO3\Fluid\Core\Variables;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

/**
 * Class StandardVariableProvider
 */
class StandardVariableProvider implements VariableProviderInterface {

	/**
	 * Variables stored in context
	 *
	 * @var array
	 */
	protected $variables = array();

	/**
	 * Variables, if any, with which to initialize this
	 * VariableProvider.
	 *
	 * @param array $variables
	 */
	public function __construct(array $variables = array()) {
		$this->variables = $variables;
	}

	/**
	 * Set the source data used by this VariableProvider. The
	 * source can be any type, but the type must of course be
	 * supported by the VariableProvider itself.
	 *
	 * @param mixed $source
	 * @return void
	 */
	public function setSource($source) {
		$this->variables = $source;
	}

	/**
	 * @return array
	 */
	public function getSource() {
		return $this->variables;
	}

	/**
	 * Get every variable provisioned by the VariableProvider
	 * implementing the interface. Must return an array or
	 * ArrayAccess instance!
	 *
	 * @return array|\ArrayAccess
	 */
	public function getAll() {
		return $this->variables;
	}

	/**
	 * Add a variable to the context
	 *
	 * @param string $identifier Identifier of the variable to add
	 * @param mixed $value The variable's value
	 * @return void
	 * @api
	 */
	public function add($identifier, $value) {
		$this->variables[$identifier] = $value;
	}

	/**
	 * Get a variable from the context. Throws exception if variable is not found in context.
	 *
	 * If "_all" is given as identifier, all variables are returned in an array,
	 * if one of the other reserved variables are given, their appropriate value
	 * they're representing is returned.
	 *
	 * @param string $identifier
	 * @return mixed The variable value identified by $identifier
	 * @api
	 */
	public function get($identifier) {
		return isset($this->variables[$identifier]) ? $this->variables[$identifier] : NULL;
	}

	/**
	 * Remove a variable from context. Throws exception if variable is not found in context.
	 *
	 * @param string $identifier The identifier to remove
	 * @return void
	 * @api
	 */
	public function remove($identifier) {
		if (array_key_exists($identifier, $this->variables)) {
			unset($this->variables[$identifier]);
		}
	}

	/**
	 * Returns an array of all identifiers available in the context.
	 *
	 * @return array Array of identifier strings
	 */
	public function getAllIdentifiers() {
		return array_keys($this->variables);
	}

	/**
	 * Checks if this property exists in the VariableContainer.
	 *
	 * @param string $identifier
	 * @return boolean TRUE if $identifier exists, FALSE otherwise
	 * @api
	 */
	public function exists($identifier) {
		return array_key_exists($identifier, $this->variables);
	}

	/**
	 * Clean up for serializing.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array('variables');
	}

	/**
	 * Adds a variable to the context.
	 *
	 * @param string $identifier Identifier of the variable to add
	 * @param mixed $value The variable's value
	 * @return void
	 */
	public function offsetSet($identifier, $value) {
		$this->add($identifier, $value);
	}

	/**
	 * Remove a variable from context. Throws exception if variable is not found in context.
	 *
	 * @param string $identifier The identifier to remove
	 * @return void
	 */
	public function offsetUnset($identifier) {
		$this->remove($identifier);
	}

	/**
	 * Checks if this property exists in the VariableContainer.
	 *
	 * @param string $identifier
	 * @return boolean TRUE if $identifier exists, FALSE otherwise
	 */
	public function offsetExists($identifier) {
		return $this->exists($identifier);
	}

	/**
	 * Get a variable from the context. Throws exception if variable is not found in context.
	 *
	 * @param string $identifier
	 * @return mixed The variable identified by $identifier
	 */
	public function offsetGet($identifier) {
		return $this->get($identifier);
	}

}
