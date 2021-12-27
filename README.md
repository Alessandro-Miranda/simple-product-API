# Página de produtos + Servidor de lista de produtos

### Status do projeto

*Em desenvolvimento* :construction:

## Descrição

Devido à necessidade de criação de páginas com listagem de produtos, para campanhas e demais necessidades, presentes no Grupo Oficial, teve-se a ideia de criar este projeto com o intuito de ter uma base contendo as principais informações dos produtos para exibição ao usuário e, dessa forma, agilizar o processo de criação de landing pages para diversas campanhas, dentro da plataforma VTEX, alterando apenas os devidos SKUS de produtos que devem estar na página da listagem de produtos.

## Objetivo

O principal objetivo do projeto é criar uma base de dados com as informações de produtos existentes, fazendo atualização contínua das informações (preço de/por, url da imagem, url detalhada do produto, categoria etc.) para que, quando necessário, conseguir consumir as informações sem a necessidade de inserção manual das informações por quem estiver criando o front ou, até mesmo, realizar a codificação de requisições para as APIs da VTEX a cada projeto recriando arquivos com todos os SKUs, EANs e etc.

## Entregáveis

- Base de dados de produtos; :construction:
- Aplicação responsável por baixar e atualizar as informações dos produtos com base na lista de SKUs existentes; :construction:
- CronJob para rodar as atualizações conforme a necessidade do projeto; :construction:
- API REST responsável por servir as informações para o front
- Layout sugerido da página de produtos; :construction:

## Features

- **Back**
    - [ ] Download de informações de produtos
    - [ ] Atualização das informações dos produtos
    - [x] Aplicação para retornar os produtos com base nos filtros solicitados
- **Front sugerido**
    - [ ] Criação da interface

## Pré-requisitos

Para o desenvolvimento do projeto, primeiramente é necessário ter em sua máquina o [Composer](https://getcomposer.org/), algum servidor como [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou o [servidor embutido do PHP](https://www.php.net/downloads.php) instalado e, também, algum banco de dados.

## Inicialização

### Server

Com o projeto já na máquina local, acesse a pasta server, pelo terminal e, então, instale as dependências utilizando o composer

>cd server && php composer.phar install

Também será necessário criar as variáveis de ambiente - na raíz da pasta server - para inicialização do banco de dados. Para criar o arquivo .env basta seguir o exemplo presente no arquivo **_.env.example_** preenchendo com as informações necessárias e criar a base de dados seguindo o Schema presente na pasta database ou criando um novo formato e realizando as adaptações necessárias no código.

### Servidor de desenvolvimento

Para dar início ao servidor de desenvolvimento basta seguir os passos do servidor utilizado ou, caso utilize o servidor embutido do PHP, basta rodar, no terminal, o comando para que seja iniciado o servidor em modo de desenvolvimento.

>composer server

Com o servidor iniciado já será possível acessar os produtos disponíveis na base através da rota */products*, através da aplicação front que esteja sendo construída ou utilizando alguma ferramenta como, por exemplo, o [Insomnia](https://insomnia.rest/download) ou [Postman](https://www.postman.com/). As informações possíveis de serem passadas via Query String são:

- *limit* - Limite de itens retornados em cada página
- *page* - página de exibição de produtos
- *productName* - nome do produto que está sendo buscado
- *discountTag* - Faixa de desconto solicitada (Aceita apenas um valor de desconto)
- *productCategories* - As categorias de produtos (Aceita mais de uma categoria separada pelo sinal de adição(+))
- *productID* - Id do produto buscado
- *sku* - Sku do produto buscado

### Produtos

Todos os produtos são identificados pelo productID e, também, pelo seu sku. Todos os produtos tem as seguintes propriedades

Campo                |  Descrição
-------------------- | --------------------
productID            |   Id único do produto
sku                  |   Sku do produto - identificador único junto (geralmente é o mesmo valor que o productID)
sellerID             |   String contendo o identificador do seller do produto (o valor padrão do seller é 1)
imageUrl             |   Url da imagem do produto
detailUrl            |   Url da página do produto
productName:         |   Nome do produto
discountTag:         |   Tag de desconto com base no valor De/Por
listPrice:           |   Preço original (De)
bestPrice:           |   Preço com desconto, se houver (Por)
productCategories:   |   Lista de categorias a que o produto pertence

## Uso

Para listar todos os produtos ou filtrar com base na queryStrig passada, pode-se utilizar a URL [https://{Your-url-here}/products](), substituindo os termos entre chaves pela URL onde a aplicação está rodando.

_obs: Por padrão o limite de produtos retornados é 10 e o máximo é 100 e a página atual sempre a primeira_

Para Realizar os filtros basta utilizar as querys no seguinte formato:

- [https://{Your-url-here}/products]() = Retorna todos os produtos, retornando a primeira página limitando em 10 produtos
- [https://{Your-url-here}/products?productName=some%20%product]() = Retorna os produtos que contenham o nome passado
- [https://{Your-url-here}/products?discountTag=40]() = Retorna os produtos que estejam em uma faixa de desconto menor ou igual a passada na query
- [https://{Your-url-here}/products?productCategories=Emagrecimento+beleza+saude]() = Retorna os produtos que pertençam à categoria passada
- [https://{Your-url-here}/products?productCategories=Emagrecimento+beleza+saude&limit=20]() = Limita o resultado da busca, com base no valor passado, de itens por página; respeitando o máximo de 100 itens por página.
- [https://{Your-url-here}/products?productCategories=Emagrecimento+beleza+saude&page=2]() = Solicita a página 2 da listagem de produtos
- [https://{Your-url-here}/products?productCategories=Emagrecimento&discountTag=30&limit=15&page=2]() = Filtra todos os produtos com valor de desconto menor ou igual ao passado e que pertençam à categoria, limitando a quantidade de retorno e página solicitada

_O parâmetro limit e page pode ser enviado em conjunto com qualquer um dos outros filtros aceitos e, quando omitidos, será sempre associado a eles o valor 10 e 1, respectivamente._

### Exemplo de retorno

Ao solicitar a url - [https://{Your-url-here}/products?productCategories=Emagrecimento+beleza+saude&limit=2](), o retorno será como o exemplo abaixo

```json
{
	"data": [
		{
			"productID": 1,
			"sku": 1,
			"sellerID": "1",
			"imageUrl": "https://image.com.br",
			"detailUrl": "https://detailUrl.com.br",
			"productName": "produto teste1",
			"discountTag": 20,
			"listPrice": 3561,
			"bestPrice": 3099,
			"productCategories": "Emagrecimento"
		},
		{
			"productID": 2,
			"sku": 2,
			"sellerID": "1",
			"imageUrl": "https://image.com.br",
			"detailUrl": "https://detailUrl.com.br",
			"productName": "produto teste2",
			"discountTag": 20,
			"listPrice": 3561,
			"bestPrice": 3099,
			"productCategories": "Beleza"
		}
	],
	"pagination": {
		"totalProducts": 21,
		"actualPage": 1,
		"totalPages": 11,
		"perPage": 2
	}
}
```

## Tecnologias e Ferramentas

- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Composer](https://getcomposer.org/)
- [Insomnia](https://insomnia.rest/download)

## Autores

- [Alessandro Miranda](https://github.com/Alessandro-Miranda) - _Ideia inicial e desenvolvimento_

## Licença

Este projeto está sob a licença MIT - veja o arquivo [LICENSE.md](https://github.com/Alessandro-Miranda/pagina-produtos/blob/main/LICENSE.md) para detalhes