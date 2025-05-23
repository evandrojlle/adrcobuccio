<p align="center">
    <a href="https://www.linkedin.com/in/evandrojlle/" target="_blank">
        <img src="https://media.licdn.com/dms/image/v2/D4D03AQGwE1Sw5gAPXg/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1718302942023?e=1746057600&v=beta&t=F0RJ1v7UzintcnID3JwPtppNZQvbVUsns_7TaufR5qQ" width="120" alt="Evandro de Oliveira">
    </a>
</p>

# Desafio Backend PHP - Grupo Adriano Cobuccio

## Sobre o Desafio.

### Tecnologias Desejadas
- PHP
- Laravel
- SQL

### Expectativas da Entrevista Técnica
Na data agendada para a entrevista, os candidatos devem:
- Ter sua aplicação em execução e pronto para teste.
- Apresentar e explicar seu processo de desenvolvimento.
- Participar de uma sessão de revisão de código, onde a solução será avaliada como se eles já
fizessem parte da equipe.

### Objetivo
Desenvolver uma API RESTful para um sistema de carteira financeira, permitindo que os usuários gerenciem seus saldos fazendo depósitos e transferências.

### Requisitos: 
- Registro e Autenticação do Usuário.
- Os usuários podem:
    - Depositar dinheiro em sua carteira.
    - Transferir dinheiro para outros usuários.
    - Receber transferências de outros usuários.

- Regras de validação:
    - Um usuário deve ter saldo suficiente antes de fazer uma transferência.
    - Se o saldo de um usuário ficar negativo devido a um problema, nenhum depósito
adicional deve ser adicionado.

- Reversões de Transações:
    - Depósitos e transferências devem ser reversíveis em caso de inconsistências ou
solicitações do usuário.

### Critérios de avaliação
- Melhores práticas de segurança.
- Qualidade e legibilidade do código (código limpo).
- Arquitetura de software (manutenção e escalabilidade).
- Tratamento de erros e registro.
- Capacidade de justificar decisões técnicas.
- Conhecimento de padrões de design e princípios SOLID.
- Modelagem de dados e design de banco de dados.

### Pontos de bônus
Os candidatos que implementarem o seguinte se destacarão:
- Configuração do Docker para consistência do ambiente local
- Testes de integração para verificar a funcionalidade da API
- Testes de unidade para validação da lógica de negócios
- Documentação de API bem estruturada
- Observabilidade (registro, monitoramento, rastreamento de erros)

## Sobre o Projeto

### Tecnologias
De acordo com as premissas do projeto, para desenvolver a API foi utilizado:
- PHP 8.3.x
- Laravel 11.x
- Banco de Dados SQLite.
    - Foi usado esse banco de dados para facilitar a importação, visto que o mesmo não exige instalação, porém exige a extensão SQLite do PHP.

### Baixando o projeto do repositório do GitHub.
- O projeto deve ser baixado do repositório do Github https://github.com/evandrojlle/adrcobuccio, usando o comando abaixo, através do seu terminal:
```git
git clone https://github.com/evandrojlle/adrcobuccio.git
```
or
```git
git clone git@github.com:evandrojlle/adrcobuccio.git
```

### Baixando as dependências do Laravel.
- Após baixar o projeto para a máquina local, ainda pelo terminal, é preciso acessar o diretório raiz do projeto e executar o composer para instalar as dependências do Laravel.
```composer
composer install
```

### Configurações
- Após instalar as dependências do Laravel, renomeie o arquivo .env.example para .env . Edite este arquivo e verifique e, se necessário, corrija as configurações de acordo com seu ambiente de trabalho.
- Foi usado banco de dado SQLite, mas se preferir, pode ser usado o banco de dados MySQL, sem problema nenhum.

Caso opte por continuar usando o SQLite, configure apenas o a constante DB_CONNECTION, conforme abaixo:
```php
DB_CONNECTION=sqlite
```
Porém se optar por usar o banco de dados MySQL, configure as contantes conforme abaixo:
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adrcobuccio
DB_USERNAME=meu_usuario
DB_PASSWORD=minha_senha
```

### Migrações
- Para criar as tabelas no banco de dados `adrcobuccio`, execute os comandos abaixo:
```php
php artisan migrate:install
php artisan migrate
```

### Carga de dados
- Para adicionar os dados de teste ao banco de dados, o comando abaixo pode ser executado:
```php
php artisan db:seed
```
ou
```php
php artisan db:seed UserSeeder
```

### Rodando o projeto.
- Após a configuração, é hora de colocar o projeto em execução. Execute o comando abaixo no seu terminal:
```
php artisa serve
```
- Este comando mostrará o endereço ao qual o aplicativo está respondendo. Copie e cole a URL no navegador.

```
 INFO  Server running on [http://127.0.0.1:8000].
```

### Postman / Insomnia
- Foram adicionadas as colections do Postman e do Insomnia no diretório `documents/` do projeto.

### Rotas.
As rotas são configuradas no arquivo routes/api.php e podem ser executadas através do POSTMAN ou INSOMNIA, da seguinte forma:
- [Criar Usuário](http://127.0.0.1:8000/apiuser/store)
    - Para criação do usuário utilize os parâmetros name, email, password e password_confirmation. Utilize o método POST.
    ```json
    {
	    "name": "Evandro de Oliveira",
	    "email": "evandrojlle@gmail.com",
	    "password": "Abc@123456",
	    "password_confirmation": "Abc@123456"
    }
    ```
    Response:
    ```json
    {
        "success": true,
	    "message": "User created successfully.",
	    "data": {
		    "id": 1
	    }
    }

    ```
- [Autenticação](http://127.0.0.1:8000/api/auth)
    - Para autenticar com o usuário, utilize o email e a senha usada na criação do usuário. Utilize o método POST
    ```json
    {
        "email": "evandrojlle@gmail.com",
        "password": "Abc@123456"
    }
    ```
    Response:
    ```json
    {
	"success": true,
	"token": "6|Uv1LsZc6a5rlksPvTeXwQNI4mbKorPNgYWxStzXDa748926a",
	"user": {
		"id": 11,
		"name": "Evandro de Oliveira",
		"email": "evandrojlle@gmail.com"
	},
	"message": "Authentication successfully!"
    }
    ```
    - Após a autenticação utilize a hash gerada no campo `token` no campo authorization do POSTMAN/INSOMNIA, com o tipo Bearer Token.

- [Edição de Usuário](http://127.0.0.1:8000/api/user/update)
    - Após realizada a autentição, é possível editar as informações do usuário. Utilize o método POST.
    - A senha somente será auterada se for enviada na request.
    
    ```json
    {
        "user_id":11,
        "name": "Evandro de Oliveira",
        "email": "evandrojlle@hotmail.com"
    }
    ```
    Response:
    ```json
    {
        "success": true,
        "message": "User updated successfully.",
        "data": {
            "id": 1'
        }
    }
    ```

- [Dados de um usuário](http://127.0.0.1:8000/api/tasks/list/6)
    - Utilize esta rota para visualizar as informações da conta do usuário, inclusive sua carteira e suas transações. Utilize o método GET.
    ```json
    {
        "success": true,
        "message": "Show item found.",
        "data": {
            "id": 1,
            "name": "Evandro de Oliveira",
            "email": "evandrojlle@gmail.com",
            "created_at": "2025-05-21T15:59:09.000000Z",
            "updated_at": "2025-05-22T17:05:59.000000Z",
            "wallet": {
                "id": 1,
                "owner_id": 1,
                "amount": 1000
            },
            "transfers": [
                {
                    "id": 1,
                    "wallet_id": 1,
                    "type_transaction": "CREDIT",
                    "amount_transaction": 1000,
                    "transfer_id": 1
                }
            ]
        }
    }
    ```

- [Lista de usuários sem filtro](http://127.0.0.1:8000/api/user/filters)
    - Utilize esta rota para visualizar as informações de todos os usuários, inclusive sua carteira e suas transações. Utilize o método GET.

    ```json
    {
        "success": true,
        "message": "Show items found.",
        "data": [
            {
                "id": 1,
                "name": "Evandro de Oliveira",
                "email": "evandrojlle@gmail.com",
                "created_at": "2025-05-21T15:59:09.000000Z",
                "updated_at": "2025-05-22T17:05:59.000000Z",
                "wallet": {
                    "id": 1,
                    "owner_id": 1,
                    "amount": 1000
                },
                "transfers": [
                    {
                        "id": 1,
                        "wallet_id": 1,
                        "type_transaction": "CREDIT",
                        "amount_transaction": 1000,
                        "transfer_id": 1
                    }
                ]
            },
            {
                ...
            }
        ]
    }
    ```

- [Lista de usuários com filtro](http://127.0.0.1:8000/api/user/filters/name=Evandro&email=hotmail)
    - Utilize esta rota para visualizar as informações usuários específicos a partir dos filtros, inclusive sua carteira e suas transações. Utilize o método GET.

    ```json
    {
        "success": true,
        "message": "Show items found.",
        "data": [
            {
                "id": 1,
                "name": "Evandro de Oliveira",
                "email": "evandrojlle@gmail.com",
                "created_at": "2025-05-21T15:59:09.000000Z",
                "updated_at": "2025-05-22T17:05:59.000000Z",
                "wallet": {
                    "id": 1,
                    "owner_id": 1,
                    "amount": 1000
                },
                "transfers": [
                    {
                        "id": 1,
                        "wallet_id": 1,
                        "type_transaction": "CREDIT",
                        "amount_transaction": 1000,
                        "transfer_id": 1
                    }
                ]
            }
        ]
    }
    ```

- [Adicionar Valores na própria carteira](http://127.0.0.1:8000/api/wallet/self)
    - Utilize esta rota para adicionar valores em sua própria carteira. Utilize o método POST.
    - O usuário deve estar autenticado para realizar a transação.

    ```json
    {
        "amount_transaction": 1000
    }
    ```
    Response:

    ```json
    {
        "success": true,
        "message": "Money deposite successfully.",
        "data": {
            "id": 1,
            "owner": "Evandro de Oliveira",
            "amount_transaction": 1000,
            "amount": 1000
        }
    }
    ```

- [Receber Valores de outros usuários](http://127.0.0.1:8000/api/wallet/other)
    - Utilize esta rota para receber valores de outro usuário. Utilize o método POST.
    - O usuário deve estar autenticado para realizar a transação.
    - O recebimento só acontecerá se o usuário possuir uma carteira.
    - O recebimento só acontecerá se o usuário possuir saldo positivo.

    ```json
    {
        "user_id": 2,
        "amount_transaction": 11.80
    }
    ```
    Response:
    ```json
    {
        "success": true,
        "message": "Credit received successfully.",
        "data": {
            "id": 1,
            "from": "Prof. Alexandre Sipes",
            "to": "Evandro de Oliveira",
            "amount_transaction": 11.8,
            "amount": 990.29
        }
    }
    ```

- [Transferir dinheiro para outros usuários](http://127.0.0.1:8000/api/wallet/transfer)
    - Utilize esta rota para adicionar valores em sua própria carteira. Utilize o método POST.
    - O envio só acontecerá se o eu possuir saldo positivo.
    - O envio acontecerá mesmo que o usuário não possua uma carteira. Neste caso a carteira será criada.

    ```json
    {
        "user_id": 3,
        "amount_transaction": 1.90
    }
    ```
    Response:
    ```json
    {
        "success": true,
        "message": "Transfer successfully.",
        "data": {
            "id": 3,
            "from": "Evandro de Oliveira",
            "to": "Mrs. Nikita Cole IV",
            "amount_transaction": 1.9,
            "amount": 1.9
        }
    }
    ```


