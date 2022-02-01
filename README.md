# API simples para listagem de informações básicas de produtos

- [English](https://github.com/Alessandro-Miranda/products-listing/blob/main/README.en.md)

## Descrição

API simples com intuíto de realizar a listagem de informações básicas de produtos tal como também a informação de paginação e total de resultados por filtro. Projeto com intenção de uso diário e, também, para estudo.

Fique a vontade para contribuir com este projeto. :smile:

## Objetivo

O principal objetivo do projeto é criar uma pequena base de dados com as informações de produtos, fazendo atualização, quando necessário, das informações (preço de/por, url da imagem, url detalhada do produto, categoria etc.) para que seja possível consumir as informações sem a necessidade de inserção manual das informações por quem estiver criando o front ou, até mesmo, realizar a codificação de requisições para as APIs da VTEX (plataforma utilizada neste projeto) a cada projeto diferente.

## Entregáveis

- Base de dados de produtos; :white_check_mark:
- Aplicação responsável por baixar e atualizar as informações dos produtos com base na lista de SKUs e EANS; :white_check_mark:
- Aplicação responsável por servir as informações dos produtos; :white_check_mark:

## Features

- [x] Download de informações de produtos
- [x] Atualização das informações dos produtos
- [x] Aplicação responsável por retornar os produtos filtrados
	- [x] Retorno de todos os produtos
	- [x] Retorno com limite de produtos
	- [x] Paginação
	- [x] Filtro por sku
	- [x] Filtro por id do produto
	- [x] Filtro por nome do produto
	- [x] Filtro por tag de desconto

## Pré-requisitos

Tenha certeza de ter instalado em sua máquina o [Composer](https://getcomposer.org/), algum servidor como [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou o [servidor embutido do PHP](https://www.php.net/downloads.php) instalado e, também, algum banco de dados - o Banco utilizado pelo projeto foi o mySQL.

## Inicialização

Com o projeto já na máquina local, acesse a pasta server, pelo terminal e, então, instale as dependências utilizando o comando `composer install` ou `php composer.phar install` e, logo após, `composer dump-autoload -o` para gerar o autoload das classes.

Também será necessário criar as variáveis de ambiente para inicialização do banco de dados. Para criar o arquivo .env basta seguir o exemplo presente no arquivo **_.env.example_** preenchendo com as informações necessárias e criar a base de dados seguindo o Schema presente na pasta database ou criando um novo formato e realizando as adaptações necessárias no código. Para baixar os produtos e atualizá-los, será necessário atualizar o código existente conforme a necessidade da plataforma utilizada e os endpoints necessários. O projeto atual foi feito utilizando a plataforma VTEX, portanto, é necessário ter os arquivos *ean.txt* e *sku.txt* para obter as informações necessárias dos produtos.

### Desenvolvimento

Inicie o servidor PHP seguindo os passos do servidor utilizado ou, caso utilize o servidor embutido do PHP, basta rodar, no terminal, o comando abaixo para que seja iniciado o servidor.

>composer server

Com o servidor iniciado já será possível acessar os produtos disponíveis na base através da rota */products*. Os parâmetros permitidos pela rota são:

- *limit* - Limite de itens retornados em cada página
- *page* - página de exibição de produtos
- *productName* - nome do produto que está sendo buscado
- *discountTag* - Faixa de desconto solicitada (Aceita apenas um valor de desconto)
- *productCategories* - As categorias de produtos (Aceita mais de uma categoria separada pelo sinal de adição(+))
- *productID* - Id do produto buscado
- *sku* - Sku do produto buscado
- *bestPrice* - Faixa de preço do produto (Aceita dois valores separados pelo sinal de adição(+)). _Obs.: O valor não deve possuir vírgulas ou pontos. Ex.: R$ 30,25 deve ser passado como 3025_

### Produtos

Todos os produtos são identificados pelo productID e, também, pelo seu sku. Todos os produtos tem as seguintes propriedades

Campo                | Tipo					| Descrição
-------------------- | ---------------------|-------------------------
productID            | integer				| Id único do produto
sku                  | integer				| Sku do produto - identificador único junto com o productID (geralmente é o mesmo valor que o productID)
sellerID             | string 				| Identificador do seller do produto (o valor padrão do seller é 1)
imageUrl             | string				| Url da imagem do produto
detailUrl            | string				| Url da página do produto
productName          | string				| Nome do produto
discountTag          | string				| Tag de desconto com base no valor De/Por
listPrice            | integer				| Preço original (De)
bestPrice            | integer				| Preço com desconto, se houver (Por)
productCategories    | string				| Lista de categorias a que o produto pertence

## Uso

Para listar ou filtrar os produtos, pode-se utilizar a URL _**/products**_.

_obs: Por padrão o limite de produtos retornados é 10 e o máximo é 100 e a página atual sempre a primeira_

Alguns exemplos de filtro e busca de produtos:

- _**/products**_ : Retorna todos os produtos, retornando a primeira página limitando em 10 produtos
- _**/products?productName=some%20%product**_ : Retorna os produtos que contenham o nome passado
- _**/products?discountTag=40**_ : Retorna os produtos com a tag de desconto solicitada obedecendo a seguinte lógica (faixa entre desconto-10 e desconto)
- _**/products?productCategories=Emagrecimento+beleza+saude**_ = Retorna os produtos que pertençam à categoria passada
- _**/products?productCategories=Emagrecimento+beleza+saude&limit=20**_ : Limita o resultado de itens por página;
- _**/products?productCategories=Emagrecimento+beleza+saude&page=2**_ : Solicita a página 2 da listagem de produtos
- _**/products?productCategories=Emagrecimento&discountTag=30&limit=15&page=2**_ : União de diversos filtros
- _**/products?bestPrice=3000+4000**_ : Faz a busca pela faixa de preço do produto

_Os parâmetros limit e page pode ser enviado em conjunto com qualquer um dos outros filtros aceitos e, quando omitidos, será sempre associado a eles o valor pdrão de 10 e 1, respectivamente._

### Exemplo de resposta

Ao solicitar a url _**/products?productCategories=Emagrecimento+beleza+saude&limit=2**_, o retorno será como o exemplo a seguir

```json
{
  "data": [
	{
	  "productID": 1,
	  "sku": 1,
	  "sellerID": "1",
	  "imageUrl": "https://image.com.br",
	  "detailUrl": "/detailUrl/p",
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
	  "detailUrl": "/detailUrl/p",
	  "productName": "produto teste2",
	  "discountTag": 20,
	  "listPrice": 3561,
	  "bestPrice": 3099,
	  "productCategories": "Beleza,Emagrecimento,Saúde"
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

### Download inicial das informações dos produtos

Para realizar o download das informações de todos os produtos, é necessário executar o arquivo _initDownload.php_ através do navegador ou via CLI. Utilizando a CLI do PHP basta executar o comando:

> php initDownload.php

O Script será iniciado em ciclos de 1000 requisições e pausa de 20 segundos para não ultrapassar o limite de requisições por minuto das APIS utilizadas no desenvolvimento do projeto.

_Alguns produtos podem retornar sem as informações de tag de desconto, valor e valor com desconto._

### Atualização dos produtos

A atualização dos produtos funciona de forma similiar ao download, bastando iniciar via CLI ou navegador a execução do arquivo (initUpdate.php). Via CLI basta executar o comando:

>php initUpdate.php

## Tecnologias e Ferramentas

- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Composer](https://getcomposer.org/)
- [Insomnia](https://insomnia.rest/download)

## Autor

- [Alessandro Miranda](https://github.com/Alessandro-Miranda) - _Ideia inicial e desenvolvimento_

## Licença

Este projeto está sob a licença MIT - veja o arquivo [LICENSE](https://github.com/Alessandro-Miranda/pagina-produtos/blob/main/LICENSE) para detalhes.
