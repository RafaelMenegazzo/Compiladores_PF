<?php

/**
 * INI -> ID AP FP INIBLOCO FIMBLOCO
 * BLOCO -> IF | WHILE | FOR 
 * IF - IF AP EXP FP INIBLOCO
 * WHILE - WHILE AP EXP FP INIBLOCO |   
 * COMANDO - ID ATRIBUICAO EXP
 * EXP - ID | CONST | EXP OP EXP
 * OP - + | - | > | < | >= | <=
 * SCANF - SCANF AP FP PV
 * PRINTF - PRINTF AP FP PV
 * 
 * 
 */

include_once "lexico.php";

class DescRecursivo{
    
    private $cont = 0;
    private $lexico;
    
     public function __construct(Lexico $lexico){
            $this->lexico = $lexico;
    }


     function term($token):bool{
        $ret = $this->lexico->getLista_tokens()[$this->cont]->getToken() == $token;
        $this->cont++;
        return $ret;
    }


   public function ini(): bool {
        return $this->term("ID")
            && $this->term("AP")
            && $this->term("FP")
            && $this->term("INIBLOCO")
            && $this->bloco()
            && $this->term("FIMBLOCO");
    }

    /** BLOCO -> IF | WHILE | FOR | COMANDO | SCANF | PRINTF */
    private function bloco(): bool {
        $anterior = $this->cont;

        if ($this->ifstmt()) return true;
        $this->cont = $anterior;

        if ($this->whilestmt()) return true;
        $this->cont = $anterior;

        if ($this->forstmt()) return true;
        $this->cont = $anterior;

        if ($this->comando()) return true;
        $this->cont = $anterior;

        if ($this->scanfstmt()) return true;
        $this->cont = $anterior;

        if ($this->printfstmt()) return true;
        $this->cont = $anterior;

        return false;
    }

    /** IF -> IF AP EXP FP INIBLOCO BLOCO FIMBLOCO */
    private function ifstmt(): bool {
        return $this->term("IF")
            && $this->term("AP")
            && $this->expe()
            && $this->term("FP")
            && $this->term("INIBLOCO")
            && $this->bloco()
            && $this->term("FIMBLOCO");
    }

    /** WHILE -> WHILE AP EXP FP INIBLOCO BLOCO FIMBLOCO */
    private function whilestmt(): bool {
        return $this->term("WHILE")
            && $this->term("AP")
            && $this->expe()
            && $this->term("FP")
            && $this->term("INIBLOCO")
            && $this->bloco()
            && $this->term("FIMBLOCO");
    }

    /** FOR -> FOR AP COMANDO PV EXP PV COMANDO FP INIBLOCO BLOCO FIMBLOCO */
    private function forstmt(): bool {
        return $this->term("FOR")
            && $this->term("AP")
            && $this->comando()
            && $this->term("PV")
            && $this->expe()
            && $this->term("PV")
            && $this->comando()
            && $this->term("FP")
            && $this->term("INIBLOCO")
            && $this->bloco()
            && $this->term("FIMBLOCO");
    }

    /** COMANDO -> ID IGUAL EXP PV */
    private function comando(): bool {
        return $this->term("ID")
            && $this->term("IGUAL")
            && $this->expe()
            && $this->term("PV");
    }

    /** SCANF -> SCANF AP FP PV */
    private function scanfstmt(): bool {
        return $this->term("SCANF")
            && $this->term("AP")
            && $this->term("FP")
            && $this->term("PV");
    }

    /** PRINTF -> PRINTF AP FP PV */
    private function printfstmt(): bool {
        return $this->term("PRINTF")
            && $this->term("AP")
            && $this->term("FP")
            && $this->term("PV");
    }

    /** EXP -> ID | CONST | EXP OP EXP */
    private function expe(): bool {
        $anterior = $this->cont;

        if ($this->term("ID")) {
            $this->opExp();
            return true;
        }
        $this->cont = $anterior;

        if ($this->constante()) {
            $this->opExp();
            return true;
        }

        return false;
    }

    /** OP EXP -> (SOMA | SUBTRAÇÃO | MAIOR | MENOR | MAIORIGUAL | MENORIGUAL) EXP */
    private function opExp(): bool {
        $tk = $this->lexico->getLista_tokens()[$this->cont] ?? null;
        if ($tk && in_array($tk->getToken(), ["SOMA","SUBTRAÇÃO","MAIOR","MENOR","MAIORIGUAL","MENORIGUAL"])) {
            $this->cont++;
            return $this->expe();
        }
        return true;
    }

    /** CONST -> TRUE | FALSE | PONTOFLUTUANTE | STRING | BOOLEAN | FLOAT | INT */
    private function constante(): bool {
        $tk = $this->lexico->getLista_tokens()[$this->cont] ?? null;
        if ($tk && in_array($tk->getToken(), ["TRUE","FALSE","PONTOFLUTUANTE","STRING","BOOLEAN","FLOAT","INT"])) {
            $this->cont++;
            return true;
        }
        return false;
    }
}




















?>