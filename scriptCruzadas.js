$(document).ready(function() {
    var palavras = []; // Array para armazenar as palavras
    var tabuleiro = []; // Matriz para representar o tabuleiro
    var tamanhoTabuleiro = 10; // Tamanho inicial do tabuleiro (10x10)

    // Função para buscar as palavras do banco de dados
    function buscarPalavras() {
        $.ajax({
            url: 'consumos.php',
            type: 'POST',
            data: { action: 'buscar_palavras_cruzadas', jogo_id: jogo_id, nivel: nivelDificuldade },
            dataType: 'json',
            success: function(response) {
                palavras = response;
                gerarTabuleiro();
            },
            error: function() {
                alert("Erro ao buscar palavras do banco de dados.");
            }
        });
    }

    // Função para gerar o tabuleiro
    function gerarTabuleiro() {
        // Define o tamanho do tabuleiro de acordo com o nível de dificuldade
        switch (nivelDificuldade) {
            case 1:
            case 2:
                tamanhoTabuleiro = 10;
                break;
            case 3:
            case 4:
                tamanhoTabuleiro = 12;
                break;
            case 5:
            case 6:
                tamanhoTabuleiro = 15;
                break;
            case 7:
            case 8:
                tamanhoTabuleiro = 18;
                break;
            case 9:
            case 10:
                tamanhoTabuleiro = 20;
                break;
            default:
                tamanhoTabuleiro = 10; // Tamanho padrão caso o nível não seja reconhecido
        }

        // Inicializa o tabuleiro com células vazias
        tabuleiro = Array(tamanhoTabuleiro).fill(null).map(() => Array(tamanhoTabuleiro).fill(null));

        // Ordena as palavras por tamanho decrescente para tentar posicionar as maiores primeiro
        palavras.sort((a, b) => b.palavra.length - a.palavra.length);

        // Posiciona as palavras no tabuleiro
        for (var i = 0; i < palavras.length; i++) {
            posicionarPalavra(palavras[i]);
        }

        // Exibe o tabuleiro na tela
        exibirTabuleiro();
    }

    // Função para posicionar uma palavra no tabuleiro
    function posicionarPalavra(palavra) {
        var palavraInserida = false;

        // Verifica se ainda há espaço disponível no tabuleiro
        if (contarCelulasVazias() < palavra.palavra.length) {
            return; // Ignora a palavra se não houver espaço suficiente
        }

        // Tenta inserir a palavra horizontalmente e verticalmente
        for (var direcao = 0; direcao < 2 && !palavraInserida; direcao++) {
            var linhaInicial = direcao === 0 ? Math.floor(Math.random() * tamanhoTabuleiro) : 0;
            var colunaInicial = direcao === 0 ? 0 : Math.floor(Math.random() * tamanhoTabuleiro);

            for (var i = linhaInicial; i < tamanhoTabuleiro && !palavraInserida; i++) {
                for (var j = colunaInicial; j < tamanhoTabuleiro && !palavraInserida; j++) {
                    if (podeInserirPalavra(palavra.palavra, i, j, direcao)) {
                        inserirPalavra(palavra.palavra, i, j, direcao);
                        palavraInserida = true;
                    }
                }
            }
        }
    }    

    // Função auxiliar para contar as células vazias do tabuleiro
    function contarCelulasVazias() {
        var contador = 0;
        for (var i = 0; i < tamanhoTabuleiro; i++) {
            for (var j = 0; j < tamanhoTabuleiro; j++) {
                if (tabuleiro[i][j] === null) {
                    contador++;
                }
            }
        }
        return contador;
    }
    // Função para verificar se pode inserir uma palavra em uma posição
    function podeInserirPalavra(palavra, linha, coluna, direcao) {
        if (direcao === 0) { // Horizontal
            if (coluna + palavra.length > tamanhoTabuleiro) {
                return false;
            }
            for (var i = 0; i < palavra.length; i++) {
                if (tabuleiro[linha][coluna + i] !== null && tabuleiro[linha][coluna + i] !== palavra[i]) {
                    return false;
                }
            }
        } else { // Vertical
            if (linha + palavra.length > tamanhoTabuleiro) {
                return false;
            }
            for (var i = 0; i < palavra.length; i++) {
                if (tabuleiro[linha + i][coluna] !== null && tabuleiro[linha + i][coluna] !== palavra[i]) {
                    return false;
                }
            }
        }
        return true;
    }

    function exibirTabuleiro() {
        var html = "";
        for (var i = 0; i < tamanhoTabuleiro; i++) {
            html += "<tr>";
            for (var j = 0; j < tamanhoTabuleiro; j++) {
                if (tabuleiro[i][j]) {
                    html += "<td class='celula-palavra' data-linha='" + i + "' data-coluna='" + j + "'>" + tabuleiro[i][j] + "&nbsp;</td>";
                } else {
                    html += "<td><input type='text' maxlength='1'></td>";
                }
            }
            html += "</tr>";
        }
        $("#tabuleiro").html(html);
    
        // Adicionar evento de clique às células com letras (usando delegação de eventos)
        $("#tabuleiro").on("click", ".celula-palavra", function() {
            var linha = $(this).data("linha");
            var coluna = $(this).data("coluna");
            mostrarDica(linha, coluna);
        });
    }

    function mostrarDica(linha, coluna) {
        // Encontra a palavra que começa na célula clicada
        var palavra = encontrarPalavra(linha, coluna);
    
        if (palavra) {
            alert(palavra.definicao); // Exibe a definição da palavra
        }
    }

    function encontrarPalavra(linha, coluna) {
        for (var i = 0; i < palavras.length; i++) {
            var palavra = palavras[i].palavra;
            var linhaPalavra = palavras[i].linha;
            var colunaPalavra = palavras[i].coluna;
            var direcaoPalavra = palavras[i].direcao;
    
            if (direcaoPalavra === 0) { // Horizontal
                if (linha === linhaPalavra && coluna >= colunaPalavra && coluna < colunaPalavra + palavra.length) {
                    return palavras[i];
                }
            } else { // Vertical
                if (coluna === colunaPalavra && linha >= linhaPalavra && linha < linhaPalavra + palavra.length) {
                    return palavras[i];
                }
            }
        }
        return null; // Retorna null se não encontrar a palavra
    }

    function inserirPalavra(palavra, linha, coluna, direcao) {
        if (direcao === 0) { // Horizontal
            for (var i = 0; i < palavra.length; i++) {
                tabuleiro[linha][coluna + i] = palavra[i];
            }
        } else { // Vertical
            for (var i = 0; i < palavra.length; i++) {
                tabuleiro[linha + i][coluna] = palavra[i];
            }
        }
    
        // Armazena a posição e direção da palavra
        palavras.find(p => p.palavra === palavra).linha = linha;
        palavras.find(p => p.palavra === palavra).coluna = coluna;
        palavras.find(p => p.palavra === palavra).direcao = direcao;
    }

    function exibirDefinicoes() {
        var html = "<ol>"; // Cria uma lista ordenada
        for (var i = 0; i < palavras.length; i++) {
            html += "<li>" + palavras[i].definicao + "</li>"; // Adiciona cada definição na lista
        }
        html += "</ol>";
        $("#definicoes").html(html); // Exibe as definições na div
    }
    
    // Adicionar evento de clique ao botão "Verificar Respostas"
    $("#verificar").click(function() {
        verificarRespostas();
    });

    function verificarRespostas() {
        var respostasCorretas = 0;

        // Percorre as células do tabuleiro
        for (var i = 0; i < tamanhoTabuleiro; i++) {
            for (var j = 0; j < tamanhoTabuleiro; j++) {
                var celula = $("#tabuleiro tr:eq(" + i + ") td:eq(" + j + ")"); // Seleciona a célula atual
                var input = celula.find("input"); // Procura um input na célula
                if (input.length > 0) { // Se houver um input na célula
                    var letraDigitada = input.val().toUpperCase(); // Obtém o valor do input
                    var letraCorreta = tabuleiro[i][j]; // Obtém a letra correta da matriz

                    if (letraDigitada === letraCorreta) {
                        celula.addClass("correta"); // Adiciona a classe "correta" à célula
                        respostasCorretas++;
                    } else {
                        celula.addClass("incorreta"); // Adiciona a classe "incorreta" à célula
                    }
                }
            }
        }

        // Exibe o resultado
        if (respostasCorretas === contarCelulasPreenchiveis()) {
            alert("Parabéns! Você acertou todas as palavras!");
        } else {
            alert("Você acertou " + respostasCorretas + " de " + contarCelulasPreenchiveis() + " palavras.");
        }
    }

    // Função auxiliar para contar as células preenchíveis do tabuleiro
    function contarCelulasPreenchiveis() {
        var contador = 0;
        for (var i = 0; i < tamanhoTabuleiro; i++) {
            for (var j = 0; j < tamanhoTabuleiro; j++) {
                if (!tabuleiro[i][j]) {
                    contador++;
                }
            }
        }
        return contador;
    }

    // Chama a função para exibir as definições após gerar o tabuleiro
    gerarTabuleiro();
    exibirDefinicoes();

    // Iniciar o jogo
    buscarPalavras();
});