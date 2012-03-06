<?php

try{
    require 'Popular.class.php';


    $popular = new Popular("127.0.0.1:3307",'root','root','teste','teste');

    
    $popular->adicionarColuna("nome","string", 10, 60);
    $popular->adicionarColuna("idade","integer", 3, 60);
    $popular->adicionarColuna("cidade","string", 15, 30);
    $popular->adicionarColuna("estado","string", 2, 2);
    $popular->adicionarColuna("dataCadastro","datetime", '2000-01-01', "2012-01-01");
    $popular->adicionarColuna("dataNascimento","date", '2000-01-01', "2012-01-01");
    $popular->adicionarColuna("limite","decimal", 100, 1000, 2);
    
    echo "Tempo: ". $popular->vai(1000000);
    

} catch (Exception $e) {
    echo utf8_encode($e->getMessage());
}
?>
