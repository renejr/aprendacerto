import nltk
from nltk.corpus import brown
from nltk import FreqDist
import mysql.connector

nltk.download('brown')
nltk.download('cmudict')

# Criar um objeto FreqDist para o corpus brown
frequencia_brown = FreqDist(brown.words())

# Dicionário para armazenar as dificuldades calculadas
dificuldades_cache = {}

# Conectar ao banco de dados
conn = mysql.connector.connect(
    host="localhost",
    port=3307,
    user="root",
    password="",
    database="aprendacerto"
)
cursor = conn.cursor()

def calcular_dificuldade(palavra):
    """Calcula a dificuldade da palavra com base em critérios."""
    if palavra in dificuldades_cache:
        return dificuldades_cache[palavra]
    try:
        frequencia = frequencia_brown[palavra] if palavra in frequencia_brown else 0
        comprimento = len(palavra)
        silabas = nltk.corpus.cmudict.dict().get(palavra, [[""]])[0]
        num_silabas = len(silabas)
        # Ajustar os pesos para cada critério
        pontuacao = (1 / (frequencia + 1)) * 1000 + comprimento + num_silabas
        dificuldades_cache[palavra] = pontuacao
        return pontuacao
    except Exception as e:
        print(f"Erro ao calcular dificuldade de '{palavra}': {e}")
        return 1000

# Níveis de dificuldade
niveis = 10
palavras_por_nivel = 100

# Loop pelos níveis
for nivel in range(1, niveis + 1):
    print(f"Processando nível {nivel}...")

    # Consulta SQL para selecionar as palavras do nível (ajuste os critérios conforme necessário)
    cursor.execute(f"""
        SELECT id, palavra
        FROM palavras
        WHERE LENGTH(palavra) BETWEEN {nivel*2} AND {nivel*2 + 3}  -- Exemplo de critério: comprimento da palavra
        LIMIT {palavras_por_nivel};
    """)
    palavras_nivel = cursor.fetchall()

    for id, palavra in palavras_nivel:
        try:
            dificuldade = calcular_dificuldade(palavra)

            # Definir o nível da palavra com base na dificuldade (ajuste os intervalos conforme necessário)
            if 0 <= dificuldade <= 10:
                nivel_palavra = 1
            elif 10 < dificuldade <= 20:
                nivel_palavra = 2
            elif 20 < dificuldade <= 30:
                nivel_palavra = 3
            elif 30 < dificuldade <= 40:
                nivel_palavra = 4
            elif 40 < dificuldade <= 50:
                nivel_palavra = 5
            elif 50 < dificuldade <= 60:
                nivel_palavra = 6
            elif 60 < dificuldade <= 70:
                nivel_palavra = 7
            elif 70 < dificuldade <= 80:
                nivel_palavra = 8
            elif 80 < dificuldade <= 90:
                nivel_palavra = 9
            else:
                nivel_palavra = 10  # Nível expert

            # Atualizar o nível da palavra no banco de dados
            cursor.execute(f"UPDATE palavras SET nivel = {nivel_palavra} WHERE id = {id}")
            conn.commit()

        except Exception as e:
            print(f"Erro ao analisar '{palavra}': {e}")

# Consulta SQL para garantir que todas as palavras tenham um nível definido
cursor.execute("""
UPDATE palavras
SET nivel = 10  -- Define o nível padrão como 'expert' para as palavras restantes
WHERE nivel IS NULL;
""")
conn.commit()

# Fechar a conexão com o banco de dados
cursor.close()
conn.close()