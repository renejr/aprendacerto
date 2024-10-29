var palavras = [];
var definicoes = [];
var palavraAtual = 0;
var tempoRestante = 300; // 5 minutos em segundos

function buscarPalavras() {
    $.ajax({
        url: 'consumos.php',
        type: 'POST',
        data: { action: 'buscar_palavras_cruzadas', jogo_id: jogo_id, nivel: level_id },
        dataType: 'json',
        success: function(response) {
            palavras = response.slice(0, 10); // Adaptado para buscar 20 palavras
            for (var i = 0; i < palavras.length; i++) {
              definicoes.push(palavras[i].definicao);
              palavras[i] = palavras[i].palavra.toUpperCase(); // Ajusta a palavra para maiúsculas
            }
            exibirPalavra();
        },
        error: function() {
            alert("Erro ao buscar palavras do banco de dados.");
        }
    });
}

function exibirPalavra() {
    console.log(palavraAtual);
    console.log(palavras[palavraAtual]);
    
    var palavra = palavras[palavraAtual];
    var letras = palavra.split('');
    var palavraHTML = "";

    for (var i = 0; i < letras.length; i++) {
        palavraHTML += '<span class="letra" data-letra="' + letras[i] + '">_</span>';
    }

    // Exibe a palavra na área principal do jogo
    $("#palavras-container").html('<div class="palavra">' + palavraHTML + '</div>');
    $("#definicoes").text(definicoes[palavraAtual]);
}

function criarTeclado() {
    var alfabeto = "abcdefghijklmnopqrstuvwxyz1234567890-=[]\\;',./~!@#$%^&*()_+{}|:\"<>?_"; // Teclado completo
    var tecladoHTML = "";
    for (var i = 0; i < alfabeto.length; i++) {
        tecladoHTML += '<button class="btn btn-primary letra">' + alfabeto[i].toUpperCase() + '</button>';
    }
    $("#teclado").html(tecladoHTML);

    // Adicionar evento de clique aos botões do teclado
    $(".letra").click(function() {
        var letra = $(this).text();
        chutarLetra(letra);
    });
}

function chutarLetra(letra) {
    console.log(palavraAtual);
    console.log(palavras[palavraAtual]);

    var palavra = palavras[palavraAtual];
    var letras = palavra.split('');
    var acertouLetra = false;

    for (var i = 0; i < letras.length; i++) {
        if (letras[i] === letra) {
            $("#palavras-container .letra:eq(" + i + ")").text(letra);
            acertouLetra = true;
        }
    }

    if (acertouLetra) {
        // Verificar se a palavra está completa
        var palavraCompleta = true;
        $("#palavras-container .letra").each(function() {
            if ($(this).text() === "_") {
                palavraCompleta = false;
                return false; // Sai do loop .each()
            }
        });

        if (palavraCompleta) {
            // Move a palavra e a definição para a lista de palavras acertadas
            $("#palavras-acertadas").append($("#palavras-container .palavra"));
            $("#palavras-acertadas").append('<div class="definicao">' + definicoes[palavraAtual] + '</div>');

            palavraAtual++;
            if (palavraAtual < palavras.length) {
                exibirPalavra(); // Exibe a próxima palavra
            } else {
                alert("Parabéns! Você completou a fase!");
                // Lógica para exibir notificação de sucesso e passar para o próximo nível
                window.location.href = "niveis.php?jogo_id=" + jogo_id; // Redireciona para niveis.php
            }
        }
    }
}

function atualizarTimer() {
    var minutos = Math.floor(tempoRestante / 60);
    var segundos = tempoRestante % 60;

    $("#tempo").text(minutos.toString().padStart(2, '0') + ":" + segundos.toString().padStart(2, '0'));

    if (tempoRestante > 0) {
        tempoRestante--;
        setTimeout(atualizarTimer, 1000);
    } else {
        alert("Tempo esgotado! A fase será reiniciada.");
        location.reload(); // Recarrega a página
    }
}

$(document).ready(function() {
    buscarPalavras();
    criarTeclado();
    atualizarTimer();
});