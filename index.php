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

function importantSeparatorExpressions(&$arrayExpression, $callbackResolver) {

    $i = 0;
    foreach($arrayExpression as $expression) {
        // se for importante é separado e retirado do array
        if (in_array($expression, ['*', '/'])) {
            // o que importa aqui é o operador
            if (is_numeric($expression)) {
                $i++;
                continue;
            }

            if (empty($arrayExpression[$i + 2])) {
                $theLastUselessOperand = $arrayExpression[$i - 1];
                $rightOperand = new Number($arrayExpression[$i + 1]);
                $arrayExpression[$i + 1] = $callbackResolver[$expression](
                    $theLastUselessOperand,
                    $rightOperand
                );
                unset($arrayExpression[$i], $arrayExpression[$i - 1]);
                break;
            }
            

            $theLastUselessOperand = $arrayExpression[$i - 1];
        if (!$arrayExpression[$i - 1] instanceof IInterpreter) {
            $theLastUselessOperand = new Number($arrayExpression[$i - 1]);
        }

        $rightOperand = $arrayExpression[$i + 1];
        if (!$arrayExpression[$i + 1] instanceof IInterpreter) {
            $rightOperand = new Number($arrayExpression[$i + 1]);
        }

            $arrayExpression[$i + 1] = $callbackResolver[$expression](
                                                $theLastUselessOperand,
                                                $rightOperand
                                            );
            unset($arrayExpression[$i], $arrayExpression[$i - 1]);

        }

        $i++;
    }

    // REINDEXANDO
    $arrayExpression = array_values($arrayExpression);

    $i = 0;
    foreach ($arrayExpression as $expression) {
        if (is_numeric($expression) || is_object($expression)) {
            $i++;
            continue;
        }

        $theLastUselessOperand = $arrayExpression[$i - 1];
        if (!$arrayExpression[$i - 1] instanceof IInterpreter) {
            $theLastUselessOperand = new Number($arrayExpression[$i - 1]);
        }

        $rightOperand = $arrayExpression[$i + 1];
        if (!$arrayExpression[$i + 1] instanceof IInterpreter) {
            $rightOperand = new Number($arrayExpression[$i + 1]);
        }

        $arrayExpression[$i + 1] = $callbackResolver[$expression](
                                            $theLastUselessOperand,
                                            $rightOperand
                                        );
        unset($arrayExpression[$i], $arrayExpression[$i - 1]);

        $i++;                                   
    }

    return end($arrayExpression);
};

$finalExpression = importantSeparatorExpressions($arithmetic, $callbackResolver);

echo $finalExpression->interpret();
 


