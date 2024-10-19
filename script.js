$(document).ready(function() {
    var Jogo = {
        palavraID: 0,
        palavraSecreta: "",
        palavraDefinicao: "",
        palavraExemplos: "",
        palavraSinonimos: "",
        letrasDigitadas: [],
        tentativas: 6,
        iniciar: function() {
            this.letrasDigitadas = [];
            this.tentativas = 6;
            
            setTimeout(function() {
                atualizarForca();
            }, 100);

            atualizarPalavraSecreta();
            atualizarLetrasDigitadas();
            criarTeclado();
        },
        reiniciar: function() {
            this.palavraID = 0;
            this.palavraSecreta = "";
            this.palavraDefinicao = "";
            this.palavraExemplos = "";
            this.palavraSinonimos = "";
            this.iniciar(); 
        }
    };

    // Função para buscar uma palavra aleatória do banco de dados
    function buscarPalavraAleatoria() {
        $.ajax({
            url: 'consumos.php',
            type: 'POST',
            data: { action: 'buscar_palavra', nivel: nivelDificuldade, jogo_id: jogo_id },
            dataType: 'json',
            success: function(response) {
                Jogo.palavraID = response.id;
                Jogo.palavraSecreta = response.palavra; 
                Jogo.palavraDefinicao = response.definicao; 
                Jogo.palavraExemplos = response.exemplos; 
                Jogo.palavraSinonimos = response.sinonimos; 
                
                console.log(Jogo.palavraID);
                console.log(Jogo.palavraSecreta);
                console.log(Jogo.palavraDefinicao);
                console.log(Jogo.palavraExemplos);
                console.log(Jogo.palavraSinonimos);
                $("#definicao").text(Jogo.palavraDefinicao);
                Jogo.iniciar(); 
            },
            error: function() {
                alert("Erro ao buscar palavra do banco de dados.");
            }
        });
    }

    // Função para atualizar a palavra secreta
    function atualizarPalavraSecreta() {
        var palavraExibida = "";
        for (var i = 0; i < Jogo.palavraSecreta.length; i++) {
            if (Jogo.letrasDigitadas.indexOf(Jogo.palavraSecreta[i]) !== -1) {
                palavraExibida += Jogo.palavraSecreta[i];
            } else {
                palavraExibida += "_";
            }
            palavraExibida += " ";
        }
        $("#palavra-secreta").text(palavraExibida);
    }

    // Função para atualizar as letras digitadas
    function atualizarLetrasDigitadas() {
        $("#letras-digitadas").text("Letras digitadas: " + Jogo.letrasDigitadas.join(", "));
    }

    // Função para criar o teclado virtual
    function criarTeclado() {
        var alfabeto = "abcdefghijklmnopqrstuvwxyz1234567890-=[]\\;',./~!@#$%^&*()_+{}|:\"<>?_";
        var tecladoHTML = "";
        for (var i = 0; i < alfabeto.length; i++) {
            tecladoHTML += '<button class="btn btn-primary letra">' + alfabeto[i] + '</button>';
        }
        $("#teclado").html(tecladoHTML);

        // Adicionar evento de clique aos botões do teclado
        $(".letra").click(function() {
            var letra = $(this).text();
            chutarLetra(letra);
        });
    }

    // Função para processar a letra digitada
    function chutarLetra(letra) {
        if (Jogo.letrasDigitadas.indexOf(letra) === -1) {
            Jogo.letrasDigitadas.push(letra);
            if (Jogo.palavraSecreta.indexOf(letra) === -1) {
                Jogo.tentativas--;
                atualizarForca();
            }
            atualizarPalavraSecreta();
            atualizarLetrasDigitadas();
            verificarFimDeJogo();
        }
    }

    // Função para atualizar a forca
    function atualizarForca() {
        var canvas = document.getElementById("forca");
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas

        ctx.beginPath();
        ctx.moveTo(20, 280);
        ctx.lineTo(180, 280);
        ctx.stroke(); // Base

        ctx.beginPath();
        ctx.moveTo(50, 280);
        ctx.lineTo(50, 20);
        ctx.stroke(); // Poste

        ctx.beginPath();
        ctx.moveTo(48, 20);
        ctx.lineTo(150, 20);
        ctx.stroke(); // Trave superior

        ctx.beginPath();
        ctx.moveTo(148, 20);
        ctx.lineTo(148, 50);
        ctx.stroke(); // Corda

        if (Jogo.tentativas < 6) {
            ctx.beginPath();
            ctx.arc(148, 70, 20, 0, Math.PI * 2);
            ctx.stroke(); // Cabeça
        }

        if (Jogo.tentativas < 5) {
            ctx.beginPath();
            ctx.moveTo(148, 90);
            ctx.lineTo(148, 120);
            ctx.stroke(); // Corpo
        }

        if (Jogo.tentativas < 4) {
            ctx.beginPath();
            ctx.moveTo(148, 100);
            ctx.lineTo(120, 130);
            ctx.stroke(); // Braço esquerdo
        }

        if (Jogo.tentativas < 3) {
            ctx.beginPath();
            ctx.moveTo(148, 100);
            ctx.lineTo(176, 130);
            ctx.stroke(); // Braço direito
        }

        if (Jogo.tentativas < 2) {
            ctx.beginPath();
            ctx.moveTo(148, 120);
            ctx.lineTo(120, 210);
            ctx.stroke(); // Perna esquerda
        }

        if (Jogo.tentativas < 1) {
            ctx.beginPath();
            ctx.moveTo(148, 120);
            ctx.lineTo(176, 210);
            ctx.stroke(); // Perna direita
        }
    }

    // Função para verificar se o jogo terminou
    function verificarFimDeJogo() {
        var palavraAdivinhada = !$("#palavra-secreta").text().includes("_");
        if (palavraAdivinhada) {
            // console.log("Palavra ID: " + Jogo.palavraID);
            // console.log("Jogo.tentativas: " + Jogo.tentativas);
            // Mostrar informações na modal 
            $("#modalPalavra").text(Jogo.palavraSecreta);
            $("#modalDefinicao").text(Jogo.palavraDefinicao);
            $("#modalExemplos").text(Jogo.palavraExemplos);
            $("#modalSinonimos").text(Jogo.palavraSinonimos);
            $("#modalInfoPalavra").modal("show");

            modificaStatus(1, Jogo.tentativas);

            Jogo.reiniciar(); // Inicia um novo jogo
        } else if (Jogo.tentativas === 0) {
            alert("Você perdeu! A palavra era: " + Jogo.palavraSecreta);

            modificaStatus(0, Jogo.tentativas);

            Jogo.reiniciar(); // Inicia um novo jogo
        }
    }

    function modificaStatus(status) {
        $.ajax({
            url: 'consumos.php',
            type: 'POST',
            data: { action: 'modifica_status', palavra_id: Jogo.palavraID, status: status, tentativas: Jogo.tentativas, jogo_id: jogo_id },
            success: function(response) {
                console.log(response);
            }
        })
    }

    // Iniciar o jogo
    buscarPalavraAleatoria();
});
