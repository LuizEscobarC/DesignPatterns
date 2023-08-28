<?php
ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);

use Source\Interpreter\Division;
use Source\Interpreter\IInterpreter;
use Source\Interpreter\Multiplication;
use Source\Interpreter\Number;
use Source\Interpreter\Subtraction;
use Source\Interpreter\Sum;

require __DIR__ . '/vendor/autoload.php';

// QUEBRA A STRING E SEPARA CADA OPERADOR E NUMERO
$arithmetic = "12 + 2 * 2 + 2 * 2 - 2 * 2 - 1 / 2 + 5 / 1 + 2 + 12 / 2 * 2 / 500";

// m(2,2)
// s(m(*2,2))
//


echo "Normal resolver result by PHP  =  " . (12 + 2 * 2 + 2 * 2 - 2 * 2 - 1 / 2 + 5 / 1 + 2 + 12 / 2 * 2 / 500);
echo PHP_EOL;
echo PHP_EOL;
echo "My result in interpreter resolver result  =  ";


$arithmetic = str_replace([')', '('], '', $arithmetic);
$arithmetic = explode(' ', $arithmetic);

$callbackResolver = [
    '*' => function ($left, $right) {
        return new Multiplication($left, $right);
    },
    '/' => function ($left, $right) {
        return new Division($left, $right);
    },
    '+' => function ($left, $right) {
        return new Sum($left, $right);
    },
    '-' => function ($left, $right) {
        return new Subtraction($left, $right);
    }
];

// a cada o operador (+-/*) é encapsulado a uma classe de interpreter com base no lado direto e esquerdo(number ou objeto de expressão já encapsulado)
function expressionArrayResolver(int &$i, array &$arrayExpressions, array $callbackResolver, string $expression)
{

    $theLastLeftUselessOperand = $arrayExpressions[$i - 1];
    if (!$arrayExpressions[$i - 1] instanceof IInterpreter) {
        $theLastLeftUselessOperand = new Number($arrayExpressions[$i - 1]);
    }

    $rightOperand = $arrayExpressions[$i + 1];
    if (!$arrayExpressions[$i + 1] instanceof IInterpreter) {
        $rightOperand = new Number($arrayExpressions[$i + 1]);
    }

    // encapsula com interpreter, reindexa e retira o que já foi
    // utilizado no array
    $arrayExpressions[$i + 1] = $callbackResolver[$expression](
        $theLastLeftUselessOperand,
        $rightOperand
    );
    unset($arrayExpressions[$i], $arrayExpressions[$i - 1]);
}

function expressionWrapperByImportance(&$arrayExpressions, $callbackResolver)
{

    $i = 0;
    // percorre operador por operador encapsulando por interpretador
    // adiciona a expressão incapsulada no lugar do operando do operando
    // da direita e retira do array o operador e o operador do lado esquerdo
    foreach ($arrayExpressions as $expression) {
        // se for importante é separado e retirado do array
        if (in_array($expression, ['*', '/'])) {
            if (is_numeric($expression) || is_object($expression)) {
                $i++;
                continue;
            }

            // se for a ultima expressão quer dizer que a expressão
            // a penultima expressão já encapsulou o numero esquerdo 
            // dessa expressao com interpreter
            if (empty($arrayExpressions[$i + 2])) {
                $theLastLeftUselessOperand = $arrayExpressions[$i - 1];
                $rightOperand = new Number($arrayExpressions[$i + 1]);
                $arrayExpressions[$i + 1] = $callbackResolver[$expression](
                    $theLastLeftUselessOperand,
                    $rightOperand
                );
                unset($arrayExpressions[$i], $arrayExpressions[$i - 1]);
                break;
            }


            expressionArrayResolver($i, $arrayExpressions, $callbackResolver, $expression);
        }
        $i++;
    }

    // reidexa o array para encapsular as expressões menos importantes
    $arrayExpressions = array_values($arrayExpressions);

    $i = 0;
    // encapsula as expressoes já encapsuladas com as operações menos importantes (+-) 
    foreach ($arrayExpressions as $expression) {
        if (is_numeric($expression) || is_object($expression)) {
            $i++;
            continue;
        }

        expressionArrayResolver($i, $arrayExpressions, $callbackResolver, $expression);

        $i++;
    }

    return end($arrayExpressions);
};

$finalExpression = expressionWrapperByImportance($arithmetic, $callbackResolver);

echo $finalExpression->interpret();
