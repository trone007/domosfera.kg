<?php
/**
 * PHP Token Reflection
 *
 * Version 1.4.0
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this library in the file LICENSE.md.
 *
 * @author Ondřej Nešpor
 * @author Jaroslav Hanslík
 */

namespace TokenReflection\Dummy;

use TokenReflection\Broker,	TokenReflection\IReflectionConstant, TokenReflection\ReflectionBase;

/**
 * Tokenized constant reflection.
 */
class ReflectionConstant implements IReflectionConstant
{
	/**
	 * Reflection broker.
	 *
	 * @var \TokenReflection\Broker
	 */
	private $broker;

	/**
	 * Class name (FQN).
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Name of the declaring class.
	 *
	 * @var string
	 */
	protected $declaringClassName;

	/**
	 * Constructor.
	 *
	 * @param string $name Constant name
	 * @param string $value Constant value
	 * @param \TokenReflection\Broker $broker Reflection broker
	 */
	public function __construct($name, $value, Broker $broker)
	{
		$this->name = ltrim($name, '\\');
		$this->value = $value;
		$this->broker = $broker;
	}

	/**
	 * Returns the name (FQN).
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns an element pretty (docblock compatible) name.
	 *
	 * @return string
	 */
	public function getPrettyName()
	{
		return $this->name;
	}

	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	public function getShortName()
	{
		$pos = strrpos($this->name, '\\');
		return false === $pos ? $this->name : substr($this->name, $pos + 1);
	}

	/**
	 * Returns the namespace name.
	 *
	 * @return string
	 */
	public function getNamespaceName()
	{
		$pos = strrpos($this->name, '\\');
		return false === $pos ? '' : substr($this->name, 0, $pos);
	}

	/**
	 * Returns if the class is defined within a namespace.
	 *
	 * @return boolean
	 */
	public function inNamespace()
	{
		return false !== strrpos($this->name, '\\');
	}

	/**
	 * Returns imported namespaces and aliases from the declaring namespace.
	 *
	 * @return array
	 */
	public function getNamespaceAliases()
	{
		return array();
	}

	/**
	 * Returns the file name the reflection object is defined in.
	 *
	 * @return null
	 */
	public function getFileName()
	{
		return null;
	}

	/**
	 * Returns the definition start line number in the file.
	 *
	 * @return null
	 */
	public function getStartLine()
	{
		return null;
	}

	/**
	 * Returns the definition end line number in the file.
	 *
	 * @return null
	 */
	public function getEndLine()
	{
		return null;
	}

	/**
	 * Returns the appropriate docblock definition.
	 *
	 * @return boolean
	 */
	public function getDocComment()
	{
		return false;
	}

	/**
	 * Magic __get method.
	 *
	 * @param string $key Variable name
	 * @return mixed
	 */
	final public function __get($key)
	{
		return ReflectionBase::get($this, $key);
	}

	/**
	 * Returns if the reflection object is internal.
	 *
	 * @return boolean
	 */
	public function isInternal()
	{
		return true;
	}

	/**
	 * Returns if the reflection object is user defined.
	 *
	 * @return boolean
	 */
	public function isUserDefined()
	{
		return false;
	}

	/**
	 * Returns if the current reflection comes from a tokenized source.
	 *
	 * @return boolean
	 */
	public function isTokenized()
	{
		return false;
	}

	/**
	 * Returns the reflection broker used by this reflection object.
	 *
	 * @return \TokenReflection\Broker
	 */
	public function getBroker()
	{
		return $this->broker;
	}

	/**
	 * Returns the name of the declaring class.
	 *
	 * @return string|null
	 */
	public function getDeclaringClassName()
	{
		return $this->declaringClassName;
	}

	/**
	 * Returns a reflection of the declaring class.
	 *
	 * @return \TokenReflection\ReflectionClass|null
	 */
	public function getDeclaringClass()
	{
		if (null === $this->declaringClassName) {
			return null;
		}

		return $this->getBroker()->getClass($this->declaringClassName);
	}

	/**
	 * Magic __isset method.
	 *
	 * @param string $key Variable name
	 * @return boolean
	 */
	final public function __isset($key)
	{
		return ReflectionBase::exists($this, $key);
	}

	/**
	 * Returns the string representation of the reflection object.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf(
			"Constant [ %s %s ] { %s }\n",
			str_replace('double', PHP_VERSION_ID >= 70000 ? 'float' : 'double', strtolower(gettype($this->getValue()))),
			$this->getName(),
			$this->getValue()
		);
	}

	/**
	 * Returns the constant value.
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns the constant value definition.
	 *
	 * @return string
	 */
	public function getValueDefinition()
	{
		return '';
	}

	/**
	 * Returns if the reflection subject is deprecated.
	 *
	 * @return boolean
	 */
	public function isDeprecated()
	{
		return false;
	}

	/**
	 * Returns if the constant definition is valid.
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		return true;
	}

}
