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
 * @author Boris Momcilovic
 */

namespace TokenReflection;

use TokenReflection\Stream\StreamBase as Stream, TokenReflection\Exception;

/**
 * Tokenized definition reflection.
 */
class ReflectionDefinition extends ReflectionConstant
{
	/**
	 * Parses the constant name.
	 *
	 * @param \TokenReflection\Stream\StreamBase $tokenStream Token substream
	 * @return \TokenReflection\ReflectionConstant
	 * @throws \TokenReflection\Exception\ParseReflection If the constant name could not be determined.
	 */
	protected function parseName(Stream $tokenStream)
	{
		// @todo

		return $this;
	}

	/**
	 * Parses the constant value.
	 *
	 * @param \TokenReflection\Stream\StreamBase $tokenStream Token substream
	 * @param \TokenReflection\IReflection $parent Parent reflection object
	 * @return \TokenReflection\ReflectionConstant
	 * @throws \TokenReflection\Exception\ParseException If the constant value could not be determined.
	 */
	private function parseValue(Stream $tokenStream, IReflection $parent)
	{
		// @todo

		return $this;
	}
}
