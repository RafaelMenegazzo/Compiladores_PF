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

    //implementação das regras da gramática para o parser;
    $regras = [
        1=> ['<S>', 1], // o número represeta a quantidade de reduces que deverão ser feitos, nesse caso, apenas 1
        3  => ['<COMANDO>', 1],                         // <COMANDO> ::= <WHILE>
        4  => ['<COMANDO>', 1],                         // <COMANDO> ::= <FOR>
        5  => ['<COMANDO>', 2],                         // <COMANDO> ::= <EXP> PV
        6  => ['<COMANDO>', 1],                         // <COMANDO> ::= <FUNC>
        7  => ['<COMANDO>', 1],                         // <COMANDO> ::= <ATR>
        8  => ['<COMANDO>', 1],                         // <COMANDO> ::= <PRINTF>
        9  => ['<COMANDO>', 1],                         // <COMANDO> ::= <SCANF>
        10 => ['<PRINTF>', 6],                          // PRINTF AP ASPAS ID ASPAS FP PV → corpo=7 tokens
        11 => ['<SCANF>', 6],                           // SCANF AP ASPAS ID ASPAS FP PV → corpo=7 tokens
        12 => ['<ATR>', 4],                             // ID IGUAL <VALOR> PV
        13 => ['<VALOR>', 1],                           // <VALOR> ::= <EXP>
        14 => ['<ATR_FOR>', 3],                         // ID IGUAL <VALOR>
        15 => ['<LIST_PAM>', 1],                        // <LIST_PAM> ::= <PARAMETRO>
        16 => ['<LIST_PAM>', 3],                        // <LIST_PAM> ::= <LIST_PAM> VIRGULA <PARAMETRO>
        17 => ['<PARAMETROS>', 1],                      // <PARAMETROS> ::= <LIST_PAM>
        18 => ['<PARAMETROS>', 0],                      // <PARAMETROS> ::= î
        19 => ['<PARAMETRO>', 2],                       // <PARAMETRO> ::= <TIPO> ID
        20 => ['<PARAMETRO>', 1],                       // <PARAMETRO> ::= ID
        21 => ['<TIPO>', 1],                            // TIPO ::= INT
        22 => ['<TIPO>', 1],                            // TIPO ::= BOOLEAN
        23 => ['<TIPO>', 1],                            // TIPO ::= STRING
        24 => ['<TIPO>', 1],                            // TIPO ::= FLOAT
        25 => ['<FOR>', 9],                             // FOR AP <ATR_FOR> PV <LOG> PV <INCDEC> FP <BLOCO>
        26 => ['<IF>', 5],                              // IF AP <LOG> FP <BLOCO> <ELSE> (contar corretamente)
        27 => ['<WHILE>', 5],                           // WHILE AP <LOG> FP <BLOCO>
        28 => ['<ELSE>', 2],                            // ELSE <BLOCO>
        29 => ['<ELSE>', 0],                            // vazio
        30 => ['<FUNC>', 5],                            // ID AP <PARAMETROS> FP PV
        31 => ['<EXP>', 3],                             // <EXP> ::= <EXP> soma <EXP2>
        32 => ['<EXP>', 1],                             // <EXP> ::= <EXP2>
        33 => ['<EXP2>', 3],                            // <EXP2> ::= <EXP2> sub <EXP3>
        34 => ['<EXP2>', 1],                            // <EXP2> ::= <EXP3>
        35 => ['<EXP3>', 3],                            // <EXP3> ::= <EXP3> MULTIPLICACAO <EXP4>
        36 => ['<EXP3>', 1],                            // <EXP3> ::= <EXP4>
        37 => ['<EXP4>', 3],                            // <EXP4> ::= <EXP4> DIVISAO <EXP5>
        38 => ['<EXP4>', 1],                            // <EXP4> ::= <EXP5>
        39 => ['<EXP5>', 1],                            // <EXP5> ::= ID
        40 => ['<EXP5>', 3],                            // <EXP5> ::= AP <EXP> FP
        41 => ['<EXP5>', 1],                            // <EXP5> ::= CONST
        42 => ['<INCDEC>', 2],                          // <INCDEC> ::= ID INC
        43 => ['<INCDEC>', 1],                          // <INCDEC> ::= <DEC>
        44 => ['<DEC>', 2],                             // <DEC> ::= ID DEC
        45 => ['<LOG>', 3],                             // <LOG> ::= <LOG> MENOR <LOG2>
        46 => ['<LOG>', 1],                             // <LOG> ::= <LOG2>
        47 => ['<LOG2>', 3],                            // <LOG2> ::= <LOG2> MAIOR <LOG3>
        48 => ['<LOG2>', 1],                            // <LOG2> ::= <LOG3>
        49 => ['<LOG3>', 3],                            // <LOG3> ::= <LOG3> MENORIGUAL <LOG4>
        50 => ['<LOG3>', 1],                            // <LOG3> ::= <LOG4>
        51 => ['<LOG4>', 3],                            // <LOG4> ::= <LOG4> MAIORIGUAL <LOG5>
        52 => ['<LOG4>', 1],                            // <LOG4> ::= <LOG5>
        53 => ['<LOG5>', 1],                            // <LOG5> ::= ID
        54 => ['<LOG5>', 1],                            // <LOG5> ::= CONST
        55 => ['<SEQ_COMANDO>', 1],                     // <SEQ_COMANDO> ::= <COMANDO>
        56 => ['<SEQ_COMANDO>', 2],                     // <SEQ_COMANDO> ::= <SEQ_COMANDO> <COMANDO>
        57 => ['<BLOCO>', 3],   
    ];

    $afd = array(
        0=>["ACTION"=>['ID' => 'S 2'], 'GOTO'=>['PROGRAMA' => 1]],
        1=> ['ACTION' => ['$'=> 'ACC'], 'GOTO'=> []],
        2=>['ACTION' => ['AP' => 'S 3'], 'GOTO' => []],
        3=>['ACTION' => ['INT' => 'S 9', 'BOOLEAN' => 'S 10', 'STRING'=> 'S 11', 'FLOAT' => 'S 12', 'FP' => 'R 18', 'ID' => 'S 8'], 
        'GOTO' => ['PARAMETROS' => '4', 'PARAMETRO' => 7, 'TIPO' => 7, 'LIST_PAM' => '5']],
        4=>['ACTION' =>['FP' => 'S 13'], 'GOTO' => []],
        5=>['ACTION' =>['FP' => 'R 17', 'VIRGULA' => 'S 14'], 'GOTO'=>[]],
        6=>['ACTION' =>['FP' => 'R 15', 'VIRGULA' => 'R 15'], 'GOTO'=>[]],
        7=>['ACTION' =>['ID' => 'S 15'], 'GOTO'=>[]],
        8=>['ACTION' =>['FP' => 'R 20', 'VIRGULA' => 'R 20'], 'GOTO'=>[]],
        9=>['ACTION' =>['ID' => 'R 21'], 'GOTO'=>[]],
        10=>['ACTION' =>['ID' => 'R 22'], 'GOTO'=>[]],
        11=>['ACTION' =>['ID' => 'R 23'], 'GOTO'=>[]],
        12=>['ACTION' =>['ID'=> 'R 24'], 'GOTO'=>[]],
        13=>['ACTION' =>['INIBLOCO' => 'S 17'], 'GOTO'=>['BLOCO' => '16']],
        14=>['ACTION' =>['INT' => 'S 9', 'BOOLEAN' => 'S 10', 'STRING'=> 'S 11', 'FLOAT' => 'S 12', 'ID' => '8'], 'GOTO'=>['PARAMETRO' => '18', 'TIPO' => '7']],
        15=>['ACTION' =>['FP' => 'R 19', 'VIRGULA' => 'R 19'], 'GOTO'=>[]],
        16=>['ACTION' =>['$' => 'R 1'], 'GOTO'=>[]],
        17=>['ACTION' =>['AP' => 'S 39', 'FOR' => 'S 31', 'WHILE' => 'S 30', 'IF' => 'S 29', 'SCANF' => 'S 35', 'PRINTF' => 'S 34', 'ID' => 'S 33', 'CONST' => 'S 40'],
         'GOTO'=>['COMANDO' => '20', 'FOR' => '23', 'WHILE' => '22', 'IF' => '21', 'FUNC' => 25, 'EXP' => '24', 'EXP2' => '32', 'EXP3' => '36', 'EXP4' => '37',
         'EXP5' => '38', 'ATR' => '26', 'SEQ_COMANDO' => 19, 'PRINTF' => '27', 'SCANF' => '28']],
        18=>['ACTION' =>['FP' => 'R 16', 'VIRGULA' => 'R 16'], 'GOTO'=>[]],
        19=>['ACTION' =>['AP' => 'S 39', 'FIMBLOCO' => 'S 41', 'FOR' => 'S 31', 'WHILE' => 'S 30', 'IF' => 'S 29', 'SCANF' => 'S 35', 'PRINTF' => 'S 34', 'ID' => 'S 33', 'CONST' => 'S 40'],
         'GOTO'=>['COMANDO' => '42', 'FOR' => '23', 'WHILE' => '22', 'IF' => '21', 'FUNC' => 25, 'EXP' => '24', 'EXP2' => '32', 'EXP3' => '36', 'EXP4' => '37',
         'EXP5' => '38', 'ATR' => '26', 'PRINTF' => '27', 'SCANF' => '28']],
        20=>['ACTION' =>['AP' => 'R 55', 'FIMBLOCO' => '55', 'FOR' => '55', 'WHILE' => '55', 'IF' => '55', 'SCANF' => '55', 'PRINTF' => '55'], 'GOTO'=>[]],
        21=>['ACTION'=>['AP'=>'R 2','CONST'=>'R 2','FIMBLOCO'=>'R 2','FOR'=>'R 2','ID'=>'R 2','IF'=>'R 2','PRINTF'=>'R 2','SCANF'=>'R 2','WHILE'=>'R 2'], 'GOTO'=>[]],
        22=>['ACTION'=>['AP'=>'R 3','CONST'=>'R 3','FIMBLOCO'=>'R 3','FOR'=>'R 3','ID'=>'R 3','IF'=>'R 3','PRINTF'=>'R 3','SCANF'=>'R 3','WHILE'=>'R 3'], 'GOTO'=>[]],
        23=>['ACTION'=>['AP'=>'R 4','CONST'=>'R 4','FIMBLOCO'=>'R 4','FOR'=>'R 4','ID'=>'R 4','IF'=>'R 4','PRINTF'=>'R 4','SCANF'=>'R 4','WHILE'=>'R 4'], 'GOTO'=>[]],
        24=>['ACTION'=>['PV'=>'S 43','soma'=>'S 44'], 'GOTO'=>[]],
        25=>['ACTION'=>['AP'=>'R 6','CONST'=>'R 6','FIMBLOCO'=>'R 6','FOR'=>'R 6','ID'=>'R 6','IF'=>'R 6','PRINTF'=>'R 6','SCANF'=>'R 6','WHILE'=>'R 6'], 'GOTO'=>[]],
        26=>['ACTION'=>['AP'=>'R 7','CONST'=>'R 7','FIMBLOCO'=>'R 7','FOR'=>'R 7','ID'=>'R 7','IF'=>'R 7','PRINTF'=>'R 7','SCANF'=>'R 7','WHILE'=>'R 7'], 'GOTO'=>[]],
        27=>['ACTION'=>['AP'=>'R 8','CONST'=>'R 8','FIMBLOCO'=>'R 8','FOR'=>'R 8','ID'=>'R 8','IF'=>'R 8','PRINTF'=>'R 8','SCANF'=>'R 8','WHILE'=>'R 8'], 'GOTO'=>[]],
        28=>['ACTION'=>['AP'=>'R 9','CONST'=>'R 9','FIMBLOCO'=>'R 9','FOR'=>'R 9','ID'=>'R 9','IF'=>'R 9','PRINTF'=>'R 9','SCANF'=>'R 9','WHILE'=>'R 9'], 'GOTO'=>[]],
        29=>['ACTION'=>['AP'=>'S 45'], 'GOTO'=>[]],
        30=>['ACTION'=>['AP'=>'S 46'], 'GOTO'=>[]],
        31=>['ACTION'=>['AP'=>'S 47'], 'GOTO'=>[]],
        32=>['ACTION'=>['FP'=>'R 32','PV'=>'R 32','soma'=>'R 32','sub'=>'S 48'], 'GOTO'=>[]],
        33=>['ACTION'=>['AP'=>'S 49','DIVISAO'=>'R 39','FP'=>'R 39','IGUAL'=>'S 50','MULTIPLICACAO'=>'R 39','PV'=>'R 39','soma'=>'R 39','sub'=>'R 39'], 'GOTO'=>[]],
        34=>['ACTION'=>['AP'=>'S 51'], 'GOTO'=>[]],
        35=>['ACTION'=>['AP'=>'S 52'], 'GOTO'=>[]],
        36=>['ACTION'=>['FP'=>'R 34','MULTIPLICACAO'=>'S 53','PV'=>'R 34','soma'=>'R 34','sub'=>'R 34'], 'GOTO'=>[]],
        37=>['ACTION'=>['DIVISAO'=>'S 54','FP'=>'R 36','MULTIPLICACAO'=>'R 36','PV'=>'R 36','soma'=>'R 36','sub'=>'R 36'], 'GOTO'=>[]],
        38=>['ACTION'=>['DIVISAO'=>'R 38','FP'=>'R 38','MULTIPLICACAO'=>'R 38','PV'=>'R 38','soma'=>'R 38','sub'=>'R 38'], 'GOTO'=>[]],
        39=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP'=>'55','EXP2'=>'32','EXP3'=>'36','EXP4'=>'37','EXP5'=>'38']],
      
        40=>['ACTION'=>['DIVISAO'=>'R 41','FP'=>'R 41','IGUAL'=>'R 41','MULTIPLICACAO'=>'R 41','PV'=>'R 41','soma'=>'R 41','sub'=>'R 41'], 'GOTO'=>[]],
        41=>['ACTION'=>['AP'=>'R 57','CONST'=>'R 57','ELSE'=>'R 57','FIMBLOCO'=>'R 57','FOR'=>'R 57','ID'=>'R 57','IF'=>'R 57','PRINTF'=>'R 57','SCANF'=>'R 57','WHILE'=>'R 57'], 'GOTO'=>[]],
        42=>['ACTION'=>['AP'=>'R 56','CONST'=>'R 56','FIMBLOCO'=>'R 56','FOR'=>'R 56','ID'=>'R 56','IF'=>'R 56','PRINTF'=>'R 56','SCANF'=>'R 56','WHILE'=>'R 56'], 'GOTO'=>[]],
        43=>['ACTION'=>['AP'=>'R 5','CONST'=>'R 5','FIMBLOCO'=>'R 5','FOR'=>'R 5','ID'=>'R 5','IF'=>'R 5','PRINTF'=>'R 5','SCANF'=>'R 5','WHILE'=>'R 5'], 'GOTO'=>[]],
        44=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP'=>'57','EXP3'=>'36','EXP4'=>'37','EXP5'=>'38']],
        45=>['ACTION'=>['AP'=>'S 63','CONST'=>'S 64'], 'GOTO'=>['EXP'=>'58','LOG'=>'59','LOG2'=>'60','LOG3'=>'61','LOG4'=>'62']],
        46=>['ACTION'=>['AP'=>'S 63','CONST'=>'S 64'], 'GOTO'=>['EXP'=>'65','LOG2'=>'59','LOG3'=>'60','LOG4'=>'61','LOG5'=>'62']],
        47=>['ACTION'=>['FP'=>'S 67'], 'GOTO'=>['ATR_FOR'=>'66']],
        48=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP3'=>'68','EXP4'=>'37','EXP5'=>'38']],
        49=>['ACTION'=>['BOOLEAN'=>'S 10','FLOAT'=>'S 12','ID'=>'S 8','INT'=>'S 9','STRING'=>'S 11'], 'GOTO'=>['LIST_PAM'=>'5','PARAMETROS'=>'69','PARAMETRO'=>'7','TIPO'=>'6']],
        50=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['ATR'=>'70','EXP'=>'71','EXP2'=>'32','EXP3'=>'36','EXP4'=>'37','EXP5'=>'38']],
        51=>['ACTION'=>['AP'=>'S 72'], 'GOTO'=>[]],
        52=>['ACTION'=>['AP'=>'S 73'], 'GOTO'=>[]],
        53=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP4'=>'74','EXP5'=>'38']],
        54=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP5'=>'75']],
        55=>['ACTION'=>['PV'=>'S 76','soma'=>'S 44'], 'GOTO'=>[]],
        56=>['ACTION'=>['DIVISAO'=>'R 39','FP'=>'R 39','IGUAL'=>'R 39','MULTIPLICACAO'=>'R 39','PV'=>'R 39','soma'=>'R 39','sub'=>'R 39'], 'GOTO'=>[]],
        57=>['ACTION'=>['FP'=>'R 31','PV'=>'R 31','soma'=>'R 31','sub'=>'S 48'], 'GOTO'=>[]],
        58=>['ACTION'=>['DEC'=>'S 77','ID'=>'S 78'], 'GOTO'=>['DADO'=>'79','INIC'=>'80']],
        59=>['ACTION'=>['AP'=>'R 46','CONST'=>'R 46','ID'=>'R 46','soma'=>'S 81','sub'=>'R 46'], 'GOTO'=>[]],
      
        60=>['ACTION'=>['AP'=>'R 48','CONST'=>'R 48','ID'=>'R 48','MULTIPLICACAO'=>'S 80','soma'=>'R 48','sub'=>'R 48'], 'GOTO'=>[]],
        61=>['ACTION'=>['AP'=>'R 50','CONST'=>'R 50','ID'=>'R 50','MENOR'=>'S 81','soma'=>'R 50','sub'=>'R 50'], 'GOTO'=>[]],
        62=>['ACTION'=>['AP'=>'R 52','CONST'=>'R 52','ID'=>'R 52','soma'=>'R 52','sub'=>'R 52'], 'GOTO'=>[]],
        63=>['ACTION'=>['AP'=>'R 53','CONST'=>'R 53','ID'=>'R 53','soma'=>'R 53','sub'=>'R 53'], 'GOTO'=>[]],
        64=>['ACTION'=>['AP'=>'R 54','CONST'=>'R 54','ID'=>'R 54','soma'=>'R 54','sub'=>'R 54'], 'GOTO'=>[]],
        65=>['ACTION'=>['DEC'=>'S 77','ID'=>'S 78'], 'GOTO'=>['DADO'=>'90','INIC'=>'82']],
        66=>['ACTION'=>['PV'=>'S 83'], 'GOTO'=>[]],
        67=>['ACTION'=>['ID'=>'S 84'], 'GOTO'=>[]],
        68=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['EXP4'=>'74','EXP5'=>'38']],
        69=>['ACTION'=>['BOOLEAN'=>'S 10','FLOAT'=>'S 12','ID'=>'S 8','INT'=>'S 9','STRING'=>'S 11'], 'GOTO'=>['LIST_PAM'=>'5','PARAMETROS'=>'69','PARAMETRO'=>'7','TIPO'=>'6']],
        70=>['ACTION'=>['PV'=>'S 86','soma'=>'S 44'], 'GOTO'=>[]],
        71=>['ACTION'=>['PV'=>'R 13','soma'=>'R 13'], 'GOTO'=>[]],
        72=>['ACTION'=>['ASPAS'=>'S 87'], 'GOTO'=>[]],
        73=>['ACTION'=>['AP'=>'S 88'], 'GOTO'=>[]],
        74=>['ACTION'=>['DIVISAO'=>'S 54','FP'=>'R 35','MULTIPLICACAO'=>'R 35','PV'=>'R 35','soma'=>'R 35','sub'=>'R 35'], 'GOTO'=>[]],
        75=>['ACTION'=>['DIVISAO'=>'R 37','FP'=>'R 37','MULTIPLICACAO'=>'R 37','PV'=>'R 37','soma'=>'R 37','sub'=>'R 37'], 'GOTO'=>[]],
        76=>['ACTION'=>['DIVISAO'=>'R 40','FP'=>'R 40','IGUAL'=>'R 40','MULTIPLICACAO'=>'R 40','PV'=>'R 40','soma'=>'R 40','sub'=>'R 40'], 'GOTO'=>[]],
        77=>['ACTION'=>['AP'=>'S 17'], 'GOTO'=>['INIC'=>'89']],
        78=>['ACTION'=>['DEC'=>'S 77','ID'=>'S 78'], 'GOTO'=>['DADO'=>'91','INIC'=>'82']],
        79=>['ACTION'=>['AP'=>'R 47','CONST'=>'R 47','ID'=>'R 47','MULTIPLICACAO'=>'S 80','soma'=>'R 47','sub'=>'R 47'], 'GOTO'=>[]],
        
        80=>['ACTION'=>['AP'=>'S 92'], 'GOTO'=>['LOG5'=>'62']],
        81=>['ACTION'=>['AP'=>'S 93'], 'GOTO'=>[]],
        82=>['ACTION'=>['AP'=>'S 17'], 'GOTO'=>['INIC'=>'94']],
        83=>['ACTION'=>['PV'=>'S 95'], 'GOTO'=>[]],
        84=>['ACTION'=>['AP'=>'S 39','CONST'=>'S 40','ID'=>'S 56'], 'GOTO'=>['ATR'=>'96','EXP'=>'71','EXP2'=>'32','EXP3'=>'36','EXP4'=>'37','EXP5'=>'38']],
        85=>['ACTION'=>['PV'=>'S 97'], 'GOTO'=>[]],
        86=>['ACTION'=>['AP'=>'R 12','CONST'=>'R 12','FIMBLOCO'=>'R 12','FOR'=>'R 12','ID'=>'R 12','IF'=>'R 12','PRINTF'=>'R 12','SCANF'=>'R 12','WHILE'=>'R 12'], 'GOTO'=>[]],
        87=>['ACTION'=>['ASPAS'=>'S 98'], 'GOTO'=>[]],
        88=>['ACTION'=>['CONST'=>'S 99'], 'GOTO'=>[]],
        89=>['ACTION'=>['AP'=>'R 29','CONST'=>'R 29','ID'=>'R 29','PV'=>'R 29','soma'=>'R 29','sub'=>'R 29'], 'GOTO'=>['DEC'=>'100']],
        90=>['ACTION'=>['AP'=>'R 45','CONST'=>'R 45','ID'=>'R 45','MULTIPLICACAO'=>'S 80','soma'=>'R 45','sub'=>'R 45'], 'GOTO'=>[]],
        91=>['ACTION'=>['AP'=>'R 47','CONST'=>'R 47','ID'=>'R 47','soma'=>'R 47','sub'=>'R 47'], 'GOTO'=>['LOG4'=>'60']],
        92=>['ACTION'=>['AP'=>'R 49','CONST'=>'R 49','ID'=>'R 49','soma'=>'R 49','sub'=>'R 49'], 'GOTO'=>['LOG3'=>'61']],
        93=>['ACTION'=>['AP'=>'R 51','CONST'=>'R 51','ID'=>'R 51','soma'=>'R 51','sub'=>'R 51'], 'GOTO'=>[]],
        94=>['ACTION'=>['AP'=>'R 27','CONST'=>'R 27','FIMBLOCO'=>'R 27','FOR'=>'R 27','ID'=>'R 27','IF'=>'R 27','PRINTF'=>'R 27','SCANF'=>'R 27','WHILE'=>'R 27'], 'GOTO'=>[]],
        95=>['ACTION'=>['AP'=>'S 102','CONST'=>'S 78','ID'=>'S 78'], 'GOTO'=>['DADO'=>'79','INIC'=>'80']],
        96=>['ACTION'=>['PV'=>'R 14'], 'GOTO'=>[]],
        97=>['ACTION'=>['AP'=>'R 30','CONST'=>'R 30','FIMBLOCO'=>'R 30','FOR'=>'R 30','ID'=>'R 30','IF'=>'R 30','PRINTF'=>'R 30','SCANF'=>'R 30','WHILE'=>'R 30'], 'GOTO'=>[]],
        98=>['ACTION'=>['AP'=>'S 103'], 'GOTO'=>[]],
        99=>['ACTION'=>['AP'=>'S 104'], 'GOTO'=>[]],

       100=>['ACTION'=>['AP'=>'R 26','CONST'=>'R 26','FIMBLOCO'=>'R 26','FOR'=>'R 26','ID'=>'R 26','IF'=>'R 26','PRINTF'=>'R 26','SCANF'=>'R 26','WHILE'=>'R 26'], 'GOTO'=>[]],
    101=>['ACTION'=>['AP'=>'R 28','CONST'=>'R 28','FIMBLOCO'=>'R 28','FOR'=>'R 28','ID'=>'R 28','IF'=>'R 28','PRINTF'=>'R 28','SCANF'=>'R 28','WHILE'=>'R 28'], 'GOTO'=>[]],
    102=>['ACTION'=>['AP'=>'S 107','ID'=>'S 106'], 'GOTO'=>['ATR_FOR'=>'108','DEC'=>'109']],
    103=>['ACTION'=>['AP'=>'R 10','CONST'=>'R 10','FIMBLOCO'=>'R 10','FOR'=>'R 10','ID'=>'R 10','IF'=>'R 10','PRINTF'=>'R 10','SCANF'=>'R 10','WHILE'=>'R 10'], 'GOTO'=>[]],
    104=>['ACTION'=>['AP'=>'R 11','CONST'=>'R 11','FIMBLOCO'=>'R 11','FOR'=>'R 11','ID'=>'R 11','IF'=>'R 11','PRINTF'=>'R 11','SCANF'=>'R 11','WHILE'=>'R 11'], 'GOTO'=>[]],
    105=>['ACTION'=>['AP'=>'R 28','CONST'=>'R 28','FIMBLOCO'=>'R 28','FOR'=>'R 28','ID'=>'R 28','IF'=>'R 28','PRINTF'=>'R 28','SCANF'=>'R 28','WHILE'=>'R 28'], 'GOTO'=>[]],
    106=>['ACTION'=>['AP'=>'S 111'], 'GOTO'=>[]],
    107=>['ACTION'=>['AP'=>'S 112','CONST'=>'S 113'], 'GOTO'=>[]],
    108=>['ACTION'=>['AP'=>'R 43','CONST'=>'R 43','FIMBLOCO'=>'R 43','FOR'=>'R 43','ID'=>'R 43','IF'=>'R 43','PRINTF'=>'R 43','SCANF'=>'R 43','WHILE'=>'R 43'], 'GOTO'=>[]],
    109=>['ACTION'=>['AP'=>'R 10','CONST'=>'R 10','FIMBLOCO'=>'R 10','FOR'=>'R 10','ID'=>'R 10','IF'=>'R 10','PRINTF'=>'R 10','SCANF'=>'R 10','WHILE'=>'R 10'], 'GOTO'=>[]],
    110=>['ACTION'=>['AP'=>'R 11','CONST'=>'R 11','FIMBLOCO'=>'R 11','FOR'=>'R 11','ID'=>'R 11','IF'=>'R 11','PRINTF'=>'R 11','SCANF'=>'R 11','WHILE'=>'R 11'], 'GOTO'=>[]],
    111=>['ACTION'=>['AP'=>'R 25','CONST'=>'R 25','FIMBLOCO'=>'R 25','FOR'=>'R 25','ID'=>'R 25','IF'=>'R 25','PRINTF'=>'R 25','SCANF'=>'R 25','WHILE'=>'R 25'], 'GOTO'=>[]],
    112=>['ACTION'=>['AP'=>'R 42','CONST'=>'R 42','FIMBLOCO'=>'R 42','FOR'=>'R 42','ID'=>'R 42','IF'=>'R 42','PRINTF'=>'R 42','SCANF'=>'R 42','WHILE'=>'R 42'], 'GOTO'=>[]],
    113=>['ACTION'=>['AP'=>'R 44','CONST'=>'R 44','FIMBLOCO'=>'R 44','FOR'=>'R 44','ID'=>'R 44','IF'=>'R 44','PRINTF'=>'R 44','SCANF'=>'R 44','WHILE'=>'R 44'], 'GOTO'=>[]],
    114=>['ACTION'=>['AP'=>'R 25','CONST'=>'R 25','FIMBLOCO'=>'R 25','FOR'=>'R 25','ID'=>'R 25','IF'=>'R 25','PRINTF'=>'R 25','SCANF'=>'R 25','WHILE'=>'R 25'], 'GOTO'=>[]],
    
    );

    $pilha = [0]; // pilha de estados
    $token = $lexico->nextToken();

    while (true) {
        $estado = end($pilha);

        if (!isset($afd[$estado]['ACTION'][$token->getToken()])) {
            echo "Erro sintático no token: ".$token->getToken();
            return false;
        }

        $move = $afd[$estado]['ACTION'][$token->getToken()];
        $acao = explode(' ', $move);

        switch ($acao[0]) {
            case 'S': 
                array_push($pilha, intval($acao[1]));
                $token = $lexico->nextToken();
                break;

            case 'R': // Reduce
                $numRegra = intval($acao[1]);
                if (!isset($regras[$numRegra])) {
                    echo "Erro: regra $numRegra não definida.";
                    return false;
                }

                $lhs = $regras[$numRegra][0];  
                $rhsSize = $regras[$numRegra][1]; 

                for ($i=0; $i<$rhsSize; $i++) {
                    array_pop($pilha);
                }

                $estadoAtual = end($pilha);
                if (!isset($afd[$estadoAtual]['GOTO'][$lhs])) {
                    echo "Erro de GOTO na redução da regra $numRegra";
                    return false;
                }

                $novoEstado = $afd[$estadoAtual]['GOTO'][$lhs];
                array_push($pilha, $novoEstado);
                // atenção: NÃO avança token aqui
                break;

            case 'ACC': // Accept
                echo "Ok - Cadeia aceita!";
                return true;

            default:
                echo "Erro";
                return false;
        }
    }
}





?>