<?php

/**
 * 
 * COMANDO -> ATR | DEC
 * ATR -> id atr EXP pv
 * EXP -> id | const
 * DEC -> tipo id pv
 * 
 */


//

$cont = 0;
$lista_tokens = ['id', 'atr', 'const', 'pv', 'tipo'];

//função de validação de terminal esperado com a lista de tokens
function term($token){
    return $GLOBALS["lista_tokens"][$GLOBALS["cont"]++] == $token; 
}

function comando(){
    $anterior = $GLOBALS["cont"];
    if(atr()){
        return true;
    } else{
        $GLOBALS["cont"] = $anterior;
        return dec();
    }
}

function atr(){
    return term('id') && term('atr') && expe() && term('pv');
}

function expe(){
    $anterior = $GLOBALS["cont"];
    if(exp1()){
        return true;
    } else{
        $GLOBALS["cont"] = $anterior;
        return exp2();
    }
}

function dec(){
    return term('tipo') && term('id') && term('pv');
}

function exp1(){
    return term('id');
}

function exp2(){
    return term('const');
}



if(comando()){
    echo "linguagem aceita";
} else{
    echo "não aceita";
}










?>