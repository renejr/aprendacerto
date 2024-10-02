import nltk
from nltk.corpus import wordnet as wn
import mysql.connector

nltk.download('wordnet')

# Conectar ao banco de dados com timeout maior
conn = mysql.connector.connect(
    host="localhost",
    port=3307,
    user="root",
    password="",
    database="aprendacerto",
    connection_timeout=300  # Tempo limite de conexão de 300 segundos
)
cursor = conn.cursor()

# Criar a tabela `palavras` (caso não exista)
cursor.execute("""
CREATE TABLE IF NOT EXISTS palavras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(255) NOT NULL,
    definicao TEXT,
    exemplos TEXT,
    sinonimos TEXT
)
""")

# Extrair todas as palavras do WordNet
palavras = []
for synset in wn.all_synsets():
    for lemma in synset.lemmas():
        palavra = lemma.name()
        definicao = synset.definition()
        exemplos = ", ".join(synset.examples())
        sinonimos = ", ".join([l.name() for l in synset.lemmas()])
        palavras.append((palavra, definicao, exemplos, sinonimos))

# Inserir as palavras no banco de dados em lotes
batch_size = 1000
for i in range(0, len(palavras), batch_size):
    batch = palavras[i:i+batch_size]
    sql = "INSERT INTO palavras (palavra, definicao, exemplos, sinonimos) VALUES (%s, %s, %s, %s)"
    cursor.executemany(sql, batch)
    conn.commit()
    print(f"Inseridas {i+len(batch)} palavras no banco de dados.")

print(f"Foram inseridas {len(palavras)} palavras no banco de dados.")

# Fechar a conexão com o banco de dados
cursor.close()
conn.close()