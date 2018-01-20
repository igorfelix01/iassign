<?php

/*
 * LInE - Laboratorio de Informatica na Educacao
 * http://line.ime.usp.br
 * 
 * Programa para simular a gravar de um conteudo de um particular iMA (o iVProgH5).
 * Na verdade aqui apenas sera' listado na tela o conteudo do arquivo.
 * 
 */

session_start();
?>


<!DOCTYPE html>
<html ng-app="ivprog">
  <head>
  <title>iVProgH5 : Visual Programming (LInE-IME-USP)</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <link href="css/ivprog.css" rel="stylesheet" media="screen" />
</head>

<body>
    <div class="header">
      <h1><img src="img/logo_ivprog.png" title="iVProg" /></h1>
    </div>

    <h2>&nbsp;iVProg : Visual Programming (LInE-IME-USP)</h2>

    <a href="http://www.usp.br/line" title="Uma das paginas do LInE">LInE</a>.
    <a href="https://github.com/LInE-IME-USP" title="LInE no GitHub">Software educacional livre</a>.
    <a href="http://www.usp.br/line/wp" title="Prototipo ambiente LInE">Interatividade na Internet para aprendizagem</a>.

<?php
print "
<h3>Resultado de envia pelo iVProgH5</h3>

Abaixo o codigo enviado pelo iVProgH5.
Se copia-lo em um arquivo de nome 'exemplo.ivph' sob o diretorio './exerc' e copiar o 'index.html' trocando a linha com
<pre>src=\"main.html?iLM_PARAM_Assignment=./exerc/exerc_ivprogh5_somar2inteiros_digitados.ivph& ... \"</pre>
por
<pre>src=\"main.html?iLM_PARAM_Assignment=./exerc/exemplo.ivph& ... \"</pre>
sua solucao sera aberta no iVProgH5.
<br/><br/>\n";
if (isset($_POST["iLM_PARAM_ArchiveContent"])) {
  print "Resultado da avaliacao automatica: <b>" . $_POST["iLM_PARAM_ActivityEvaluation"] . "</b><br/><br/>\n";
  $src = $_POST["iLM_PARAM_ArchiveContent"];
  $id = time();
  $_SESSION["src_".$id] = $src;
  print "<pre>" . $src . "</pre>";
  }
else
 print "ERRO: variavel POST 'iLM_PARAM_ArchiveContent' nao foi definida ou esta vazia!";
?>

  <div class="foot"><center>
   <a href="http://www.ime.usp.br/line">LInE</a> |
   <a href="http://www.matematica.br">iM&aacute;tica</a> |
   <a href="http://www.usp.br/line/mooc">MOOC</a>
  </center></div>

  </body>
</html>