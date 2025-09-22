<?php


/*
Linguagem para derivação de Tokens

<S> ::= <PROGRAMA>;
<PROGRAMA> ::= ID AP <PARAMETROS> FP <BLOCO>;
<COMANDO> ::= <IF> | <WHILE> | <FOR> | <EXP> PV| <FUNC> | <ATR> | <PRINTF> | <SCANF>;
<PRINTF>::= PRINTF AP ASPAS ID ASPAS FP PV;
<SCANF>::= SCANF AP ASPAS ID ASPAS FP PV;
<ATR>::= ID IGUAL <VALOR> PV;
<VALOR> ::= <EXP>;
<ATR_FOR>::= ID IGUAL <VALOR>;
<LIST_PAM> ::=  <PARAMETRO> | <LIST_PAM> VIRGULA <PARAMETRO>;
<PARAMETROS> ::= <LIST_PAM> | î ;
<PARAMETRO> ::= <TIPO> ID | ID;
<TIPO> ::= INT | BOOLEAN | STRING | FLOAT;
<FOR> ::= FOR AP <ATR_FOR> PV <LOG> PV <INCDEC> FP <BLOCO>;
<IF> ::= IF AP <LOG> FP <BLOCO> <ELSE>;
<WHILE> ::= WHILE AP <LOG> FP <BLOCO>;
<ELSE>::= ELSE <BLOCO> | î;
<FUNC>::= ID AP <PARAMETROS> FP PV;
<EXP>::= <EXP> soma <EXP2> | <EXP2>;
<EXP2> ::= <EXP2> sub <EXP3> | <EXP3>;
<EXP3> ::= <EXP3> MULTIPLICACAO <EXP4> | <EXP4>;
<EXP4> ::= <EXP4> DIVISAO <EXP5> | <EXP5>;
<EXP5> ::= ID| AP <EXP> FP | CONST;
<INCDEC>::= ID INC | <DEC>;
<DEC>::=  ID DEC;
<LOG>::= <LOG> MENOR <LOG2> | <LOG2>;
<LOG2>::= <LOG2> MAIOR <LOG3> | <LOG3>;
<LOG3>::= <LOG3> MENORIGUAL <LOG4> | <LOG4>;
<LOG4>::= <LOG4> MAIORIGUAL <LOG5> | <LOG5>;
<LOG5>::= ID | CONST;
<SEQ_COMANDO>::= <COMANDO> | <SEQ_COMANDO> <COMANDO>;
<BLOCO>::= INIBLOCO <SEQ_COMANDO> FIMBLOCO;
 */


function parser(Lexico $lexico){

    $afd = array(
        0=>["ACTION"=>['PROGRAMA' => 'S 2'], 'GOTO'=>[6=>['$' => 1]]],
        1=> ['ACTION' => ['$'=> 'ACC'], 'GOTO'=> []]
    );

     $pilha = array();
    array_push($pilha,0);
    while ($token = $lexico->nextToken()){
        if (array_key_exists( $token->getToken(), $afd[end($pilha)]['ACTION']))
            $move = $afd[end($pilha)]['ACTION'][$token->getToken()];
        else 
            return false;
        $acao = explode(' ',$move);
        switch($acao[0]){
            case 'S': // Shift - Empilha e avança o ponteiro
                array_push($pilha,$acao[1]);
                break;
            case 'R': // Reduce - Desempilha e Desvia (para indicar a redução)                                                                                                                                                                                                                                                                                                                                                                       
                for ($j = 0; $j<$acao[1]; $j++)
                    array_pop($pilha);
                $desvio = $afd[end($pilha)]['GOTO'][$acao[2]][$token->getToken()];
                array_push($pilha,$desvio);
                $lexico->nextToken();
                break;
            case 'ACC': // Accept
                echo 'Ok';
                return true;
            default:
                echo 'Erro';
                return false;
        }
    }
    return false;
}




?>