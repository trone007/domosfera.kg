<?php
namespace AppBundle\DQL;
use \Doctrine\ORM\Query\AST\Functions\FunctionNode;
use \Doctrine\ORM\Query\Lexer;
/**
 * MysqlRound
 * Реализация функции ROUND в доктрине
 * @uses FunctionNode
 * @author Julien DENIAU <julien.deniau@mapado.com>
 */
class Round extends FunctionNode
{
    /**
     * simpleArithmeticExpression
     *
     * @var mixed
     * @access public
     */
    public $simpleArithmeticExpression;
    /**
     * roundPrecission
     *
     * @var mixed
     * @access public
     */
    public $roundPrecission;
    /**
     * getSql
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @access public
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'ROUND(' .
            $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression) .','.
            $sqlWalker->walkStringPrimary($this->roundPrecission) .
            ')';
    }
    /**
     * parse
     *
     * @param \Doctrine\ORM\Query\Parser $parser
     * @access public
     * @return void
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->roundPrecission = $parser->ArithmeticExpression();
        if ($this->roundPrecission == null) {
            $this->roundPrecission = 0;
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}