<?php

 /**
 * Arquivo que contem a classe de Popular
 *
 *
 * PHP 5
 *
 * Copyright (c) 2012 Marmottajr Twitter: @marciomottajr
 * @author     Márcio R. Motta Júnior <marcio@camposmotta.com.br>
 * @version    v 1.0 06/03/2012 10:17:00
 * @filesource
 **/

class Popular {
    
    private $conexao = '';
    private $host;
    private $user;
    private $password;
    private $database;
    private $tipos = array('integer','string','decimal','date','datetime');
    private $limiteBulk = '500';
    private $tabela;
    private $campos = array();

    
    
    public function __construct($host,$user,$password,$database,$tabela){
        
        set_time_limit(-1);
        
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->tabela = $tabela;
        
    }
    private function conectar(){
        if($conexao = @mysql_connect($this->host, $this->user, $this->password)){
            if(@mysql_select_db($this->database)){
                return true;
            } else {
              throw new Exception("Erro: " .mysql_errno() . " - " . mysql_error() );  
            }
        } else {
          throw new Exception("Erro: " .mysql_errno() . " - " . mysql_error() );  
        }
        
    }
    
    private function execucao(){
        $sec = explode(" ",microtime());
                $tempo = $sec[1] + $sec[0];
        return $tempo;
    }
    
    
    public function adicionarColuna($nome,$tipo,$min,$max,$casasDecimais = ''){
        
        if(in_array($tipo, $this->tipos)){
            $this->campos[] = array($nome,$tipo,$min,$max,$casasDecimais);
        } else {
            throw new Exception("Erro: Tipo de coluna inválida"); 
        }
        
    }
    
    private function sorteia($tipo,$min,$max,$casasDecimais = ''){
        $texto = "";
        
        if($tipo == 'string'){
            
            $caracteres = "abcdefghijklmnopqrstuwxyz  ";
            $max = rand($min, $max);
            for($i=0;$i<$max;$i++){
                $texto .= $caracteres[rand(0, strlen($caracteres))];
            }
            
        } else if($tipo == 'integer'){
            
           $texto = rand($min, $max);
           
        } else if($tipo == 'decimal'){
            
           $texto = rand($min, $max);
           
           $maxCasas = '';
           
           for($i=0;$i<$casasDecimais;$i++){
               $maxCasas .= "9";
           }
           
           $casas = rand(0,$maxCasas);
           $casas = sprintf("%0".$casasDecimais."d", $casas);
           
           $texto .= ".". $casas;
           
        } else if($tipo == 'date'){
            
           $texto = $this->makeRandomDateInclusive($min,$max);
           
        } else if($tipo == 'datetime'){
            
           $texto = $this->makeRandomDateInclusive($min,$max);
           
           $hora = sprintf("%02d", rand(0,23));
           $minuto = sprintf("%02d", rand(0,59));
           $segundo = sprintf("%02d", rand(0,59));
           
           $texto .= " " . $hora. ":".$minuto. ":".$segundo;
           
        }
        
        return $texto;
    }
    
    private function makeRandomDateInclusive($startDate,$endDate){
        $days = round((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
        $n = rand(0,$days);
        return date("Y-m-d",strtotime("$startDate + $n days"));    
    }
    
    
    public function vai($totalRegistros){
        
        $tempoInicio = $this->execucao();
       
        $this->conectar();
        
        $sqlInicio = "insert into " . $this->tabela . " (";
        foreach ($this->campos as $key => $value) {
            $sqlInicio .= $value[0] . ",";
        }
        $sqlInicio = substr($sqlInicio,0,-1) . ") values ";
        
        $registros = "";
        
        for($i=1;$i<=$totalRegistros;$i++){
            
            $registro = "(";
            
            foreach ($this->campos as $key => $value) {
                $valor = $this->sorteia($value[1], $value[2], $value[3], $value[4]);
                if($value[1] == 'integer'){
                    $registro .= $valor . ",";
                } else {
                    $registro .= "'". $valor . "',";
                }
                
            }
            
            $registro = substr($registro,0,-1) . ")";
            $registros .= $registro . ",";
            
            if($i % $this->limiteBulk == 0){
                $registros = substr($registros,0,-1);
                if(!mysql_query($sqlInicio . $registros)){
                    throw new Exception("Erro: " .mysql_errno() . " - " . mysql_error() );
                }
                $registros = "";
            }
        }
        
        if ($registros != ""){
            $registros = substr($registros,0,-1);
            if(!mysql_query($sqlInicio . $registros)){
                throw new Exception("Erro: " .mysql_errno() . " - " . mysql_error() );
            }
        }
        
        $tempoFim = $this->execucao();
        
        return number_format(($tempoFim-$tempoInicio),3);
        //retorna o tempo de execuçao em milesegundos
        
        
    }
    
    
}

?>
