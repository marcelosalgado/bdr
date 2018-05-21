<?php

// questão 1, FizzBuzz
for ($i = 1; $i <= 100; $i++) {
    $text = '';
    if ($i % 3 == 0) {
        $text = 'Fizz';
    }
    if ($i % 5 == 0) {
        $text .= 'Buzz';
    }

    echo $text == '' ? $i : $text;
}
