$(document).ready(function() {
    var Jogo = {
        palavraID: 0,
        palavraSecreta: "",
        palavraDefinicao: "",
        palavraExemplos: "",
        palavraSinonimos: "",
        letrasDigitadas: [],
        tentativas: 6,
        acertos:0,
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

    var tempo = 0; // Variável para armazenar o tempo
    var timerInterval; // Variável para controlar o intervalo do timer

    // Função para iniciar o timer
    function iniciarTimer() {
        timerInterval = setInterval(function() {
            tempo++;
            $("#tempo").text(tempo); // Atualiza o tempo na tela
        }, 1000); // Executa a cada 1 segundo
    }

    // Função para reiniciar o timer
    function reiniciarTimer() {
        clearInterval(timerInterval); // Limpa o intervalo do timer
        tempo = 0; // Reinicia o tempo
        $("#tempo").text(tempo); // Atualiza o tempo na tela
        iniciarTimer(); // Inicia o timer novamente
    }

    // Função para mostrar a dica
    function mostrarDica() {
        $("#definicao").show(); // Mostra a definição
    }

    // Adicionar evento de clique ao botão "Dica"
    $("#dica").click(function() {
        mostrarDica();
    });

    // Função para buscar uma palavra aleatória do banco de dados
    function buscarPalavraAleatoria() {
        $.ajax({
            url: 'consumos.php',
            type: 'POST',
            data: { action: 'buscar_palavra', nivel: nivelDificuldade, jogo_id: jogo_id },
            dataType: 'json',
            success: function(response) {
                $("#definicao").hide();
                Jogo.palavraID = response.id;
                Jogo.palavraSecreta = response.palavra; 
                Jogo.palavraDefinicao = response.definicao; 
                Jogo.palavraExemplos = response.exemplos; 
                Jogo.palavraSinonimos = response.sinonimos; 
                
                $("#definicao").text(Jogo.palavraDefinicao);
                Jogo.iniciar(); 
                reiniciarTimer();
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

            // Desabilita o botão da letra clicada
            $(".letra:contains('" + letra + "')").prop('disabled', true);

            if (Jogo.palavraSecreta.indexOf(letra) === -1) {
                Jogo.tentativas--;
                atualizarForca();
            }
            atualizarPalavraSecreta();
            atualizarLetrasDigitadas();
            verificarFimDeJogo();
        }
    }

    function atualizarForca() {
        var canvas = document.getElementById("forca");
        canvas.width = 400;  // Define a largura do canvas
        canvas.height = 400; // Define a altura do canvas
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas

        // Criar o degradê
        var gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, "yellow"); // Amarelo no topo
        gradient.addColorStop(0.5, "orange"); // Laranja no meio
        gradient.addColorStop(1, "red"); // Vermelho no fundo

        // Preencher o fundo com o degradê
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    
        // Base
        ctx.beginPath();
        ctx.lineWidth = 8;
        ctx.strokeStyle = "#8B4513";
        ctx.moveTo(20, 380);
        ctx.lineTo(380, 380);
        ctx.stroke();
    
        // Poste
        ctx.beginPath();
        ctx.moveTo(100, 380);
        ctx.lineTo(100, 20);
        ctx.stroke();
    
        // Trave superior
        ctx.beginPath();
        ctx.moveTo(98, 20);
        ctx.lineTo(250, 20);
        ctx.stroke();
    
        // Corda
        ctx.beginPath();
        ctx.lineWidth = 3;
        ctx.moveTo(248, 20);
        ctx.lineTo(248, 50);
        ctx.stroke();
    
        // Cabeça
        if (Jogo.tentativas < 6) {
            ctx.beginPath();
            ctx.lineWidth = 2;
            ctx.strokeStyle = "black";
            ctx.arc(248, 80, 30, 0, Math.PI * 2);
            ctx.stroke();
    
            // Olhos
            if (Jogo.tentativas > 3) {
                // Olhos normais
                ctx.beginPath();
                ctx.arc(233, 70, 5, 0, Math.PI * 2); // Olho esquerdo
                ctx.fillStyle = "black";
                ctx.fill();
    
                ctx.beginPath();
                ctx.arc(263, 70, 5, 0, Math.PI * 2); // Olho direito
                ctx.fill();
            } else if (Jogo.tentativas === 3) {
                // Olhos preocupados
                ctx.beginPath();
                ctx.arc(233, 75, 5, 0, Math.PI * 2); // Olho esquerdo
                ctx.fillStyle = "black";
                ctx.fill();
    
                ctx.beginPath();
                ctx.arc(263, 75, 5, 0, Math.PI * 2); // Olho direito
                ctx.fill();
    
                ctx.beginPath();
                ctx.moveTo(228, 80);
                ctx.lineTo(238, 85); // Sobrancelha esquerda
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(258, 80);
                ctx.lineTo(268, 85); // Sobrancelha direita
                ctx.stroke();
            } else if (Jogo.tentativas === 2) {
                // Olhos arregalados
                ctx.beginPath();
                ctx.arc(233, 70, 7, 0, Math.PI * 2); // Olho esquerdo
                ctx.fillStyle = "black";
                ctx.fill();
    
                ctx.beginPath();
                ctx.arc(263, 70, 7, 0, Math.PI * 2); // Olho direito
                ctx.fill();
            } else if (Jogo.tentativas === 1) {
                // Olhos fechados
                ctx.beginPath();
                ctx.moveTo(228, 70);
                ctx.lineTo(238, 70); // Olho esquerdo
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(258, 70);
                ctx.lineTo(268, 70); // Olho direito
                ctx.stroke();
            } else {
                // Olhos em X (morto)
                ctx.beginPath();
                ctx.moveTo(228, 65);
                ctx.lineTo(238, 75); // Olho esquerdo
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(228, 75);
                ctx.lineTo(238, 65);
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(258, 65);
                ctx.lineTo(268, 75); // Olho direito
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(258, 75);
                ctx.lineTo(268, 65);
                ctx.stroke();
            }
    
            // Boca
            if (Jogo.tentativas > 4) {
                // Sorriso
                ctx.beginPath();
                ctx.arc(248, 90, 10, 0, Math.PI);
                ctx.stroke();
            } else if (Jogo.tentativas === 4) {
                // Boca neutra
                ctx.beginPath();
                ctx.moveTo(238, 90);
                ctx.lineTo(258, 90);
                ctx.stroke();
            } else if (Jogo.tentativas === 3) {
                // Boca triste
                ctx.beginPath();
                ctx.arc(248, 95, 10, Math.PI, 0);
                ctx.stroke();
            } else if (Jogo.tentativas === 2) {
                // Boca aberta
                ctx.beginPath();
                ctx.arc(248, 90, 10, 0, Math.PI * 2);
                ctx.stroke();
            } else if (Jogo.tentativas === 1) {
                // Boca aberta com a língua para fora
                ctx.beginPath();
                ctx.arc(248, 90, 10, 0, Math.PI * 2);
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(248, 95);
                ctx.lineTo(248, 105);
                ctx.stroke();
            } else {
                // Boca aberta com a língua para fora (morto)
                ctx.beginPath();
                ctx.arc(248, 100, 10, 0, Math.PI * 2);
                ctx.stroke();
    
                ctx.beginPath();
                ctx.moveTo(248, 105);
                ctx.lineTo(248, 120);
                ctx.stroke();
            }
        }
    
        // Corpo
        if (Jogo.tentativas < 5) {
            ctx.beginPath();
            ctx.moveTo(248, 110);
            ctx.lineTo(248, 250);
            ctx.stroke();
        }
    
        // Braços
        if (Jogo.tentativas < 4) {
            ctx.beginPath();
            ctx.moveTo(248, 140);
            ctx.lineTo(200, 180);
            ctx.stroke();
        }
    
        if (Jogo.tentativas < 3) {
            ctx.beginPath();
            ctx.moveTo(248, 140);
            ctx.lineTo(296, 180);
            ctx.stroke();
        }
    
        // Pernas
        if (Jogo.tentativas < 2) {
            ctx.beginPath();
            ctx.moveTo(248, 250);
            ctx.lineTo(200, 300);
            ctx.stroke();
        }
    
        if (Jogo.tentativas < 1) {
            ctx.beginPath();
            ctx.moveTo(248, 250);
            ctx.lineTo(296, 300);
            ctx.stroke();
        }
    }

    // Função para verificar se o jogo terminou
    function verificarFimDeJogo() {
        var palavraAdivinhada = !$("#palavra-secreta").text().includes("_");
        if (palavraAdivinhada) {

            // Mostrar informações na modal 
            Jogo.acertos += 1;
            
            $("#modalPalavra").text(Jogo.palavraSecreta);
            $("#modalDefinicao").text(Jogo.palavraDefinicao);
            $("#modalExemplos").text(Jogo.palavraExemplos);
            $("#modalSinonimos").text(Jogo.palavraSinonimos);
            $("#modalAcertos").text(Jogo.acertos);

            if(Jogo.acertos >= 5){ 
                $("#avisoAcertos").text("");
                if(nivelDificuldade+1 <= 10){
                    $("#modalAcertos").html('<a href="forca.php?jogo_id=' + jogo_id + '&level_id=' + (nivelDificuldade+1) + '" class="btn btn-primary">Parabens! Você subiu de nível!</a>');
                }
            }

            // Mostrar modal
            $("#modalInfoPalavra").modal("show");

            modificaStatus(1, Jogo.tentativas);

            //Jogo.reiniciar(); // Inicia um novo jogo
            buscarPalavraAleatoria();
        } else if (Jogo.tentativas === 0) {
            setTimeout(function() {
                alert("Você perdeu! A palavra era: " + Jogo.palavraSecreta);
            }, 100);
            
            modificaStatus(0, Jogo.tentativas);

            //Jogo.reiniciar(); // Inicia um novo jogo
            buscarPalavraAleatoria();
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
