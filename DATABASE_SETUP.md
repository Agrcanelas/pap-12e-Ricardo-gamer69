# Base de Dados DOA+ - Instruções de Instalação

## 📋 Pré-requisitos
- XAMPP instalado e em funcionamento
- MySQL/MariaDB ativo
- phpMyAdmin (incluído no XAMPP)

## 🚀 Passos para Configurar a Base de Dados

### 1. Criar a Base de Dados + Admin (Tudo num Ficheiro!)
Tens dois métodos à escolha:

#### Método A: Via phpMyAdmin (Recomendado)
1. Abre o navegador e vai a `http://localhost/phpmyadmin/`
2. Clica em **"Importar"** no menu superior
3. Clica em **"Escolher Ficheiro"** e seleciona `doa_plus.sql`
4. Clica em **"Executar"** ou **"Import"**
5. Aguarda a conclusão (deve aparecer mensagem de sucesso)
6. **Pronto! Base de dados + admin já estão criados!**

#### Método B: Via Linha de Comando
```powershell
cd c:\xampp\mysql\bin
mysql -u root < "c:\xampp\htdocs\pap-12e-Ricardo-gamer69\doa_plus.sql"
```
(Deixa a password em branco e pressiona Enter)

## 🔐 Utilizadores Pré-criados

Após importar `doa_plus.sql`, **apenas a conta de admin estará criada automaticamente**:

### Admin (Pré-criado)
- **Email:** admin@doaplus.pt
- **Senha:** admin123
- **Tipo:** Admin

**⚠️ IMPORTANTE:** Muda a senha de admin após o primeiro login!

## 🗂️ Estrutura da Base de Dados

### Tabelas Criadas:

1. **utilizadores** - Armazena informações de utilizadores
   - id, nome, email, senha, tipo_utilizador, data_registo, ativo

2. **campanhas** - Campanhas de donativos
   - id, titulo, descricao, categoria, valor_objetivo, valor_angariado, instituicao, id_criador, datas, status

3. **doacoes** - Registo de doações
   - id, id_campanha, id_doador, montante, data_doacao, mensagem, anonimo

4. **pagamentos** - Registo de pagamentos
   - id, id_doacao, metodo_pagamento, referencia, status, data_pagamento

5. **atualizacoes_campanha** - Histórico de atualizações
   - id, id_campanha, id_autor, titulo, conteudo, data_atualizacao

## ⚙️ Configuração do PHP

O ficheiro `config.php` já está configurado para:
- **Host:** localhost
- **Database:** doa_plus
- **User:** root
- **Password:** (vazio)

Se precisares mudar estas credenciais, edita o ficheiro `config.php`

## 🧪 Teste da Conexão

Visita `http://localhost/pap-12e-Ricardo-gamer69/` para testar se tudo está a funcionar corretamente.

## ✅ Funcionalidades Implementadas

- ✅ Conta de admin pré-criada (admin@doaplus.pt)
- ✅ Sistema de registo de utilizadores com encriptação de senha
- ✅ Sistema de login com sessões
- ✅ Criação de campanhas (salva na BD)
- ✅ Listagem de campanhas ativas da base de dados
- ✅ Página individual de campanha com dados dinâmicos
- ✅ Cálculo automático de percentagens de angariação
- ✅ Registo de doações (quando implementado)
- ✅ Tudo criado pelos utilizadores após registo

## 🐛 Troubleshooting

### Erro: "Conexão recusada"
- Verifica se o MySQL está em funcionamento no XAMPP
- Verifica se as credenciais em `config.php` estão corretas

### Erro: "Base de dados não encontrada"
- Executa novamente o comando de importação de `doa_plus.sql`
- Verifica se o ficheiro SQL foi completamente executado

### Erro ao inserir dados
- Verifica se as senhas estão encriptadas corretamente
- Verifica se não há duplicate key errors nas campanhas

## � Fluxo de Utilização da Plataforma

1. **Importa `doa_plus.sql`** → Cria BD + tabelas + admin automaticamente ✅
2. **Login de Admin** → admin@doaplus.pt / admin123
3. **Novo Utilizador regista-se** → registo.php (Doador ou Instituição)
4. **Login do Utilizador** → login.php com as suas credenciais
5. **Criar Campanha** → criar-campanha.php (se for instituição)
6. **Fazer Doações** → campanhas.php (qualquer doador pode contribuir)

## �📝 Notas Importantes

- **Apenas o admin é pré-criado** - Todas as outras contas e campanhas são criadas pelos utilizadores
- Senha do admin: `admin123` (pode ser alterada depois)
- As senhas dos utilizadores são encriptadas com bcrypt (PASSWORD_DEFAULT)
- Cada campanha começa em status `pendente` e deve ser aprovada antes de aparecer
- As doações são registadas e associadas à campanha e ao doador

---

**Pronto!** Apenas 1 ficheiro a importar (`doa_plus.sql`) e tudo fica pronto! 🎉
- Base de dados ✅
- Tabelas ✅
- Conta de admin ✅

**Agora é com os utilizadores!** 🚀
