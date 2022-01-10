# Products Listing + Product page

- [Portuguese](https://github.com/Alessandro-Miranda/products-listing/blob/main/README.md)

### Project state

*Under development* :construction:

## Description

Simple API to list basic product information as well as pagination and filter result quantity information. Project intended for daily use and also for study.

Feel free to contribute to this project. :smile:

## Goals

The main goal is create a small database with product informations, updating, as necessary, informations (price of/per, image url, detail product url, category etc.) so that you can consume the informations without insert the manually by who is creating the front-end or it coding new request to VTEX APIs (plataform used in this project) at each different project.

## Deliverables

- Products database; :white_check_mark:
- Application to download and update products information based at the sku and EANS list; :white_check_mark:
- Application to delivery product informations; :white_check_mark:
- Example layout (Mobile and Web). :construction:

## Features

- **Back**
    - [x] Product information download
    - [x] Product information update
	- [x] Application for delivering filtered products
		- [x] Return all products
		- [x] Limit products per request
		- [x] Pagination
		- [x] Filter by sku
		- [x] Filter by product ID
		- [x] Filter by product name
		- [x] Filter by discount tag
- **Front**
    - [ ] Example layout (Mobile and Desktop)

## Requirements

Make sure you have installed [Composer](https://getcomposer.org/), any PHP server as a [XAMPP](https://www.apachefriends.org/pt_br/index.html) or [Built-in web server](https://www.php.net/downloads.php) 

## Getting started

### Server

With the project in your machine, change to server folder, from CMD and, using composer, install all necessary dependencies

```bash
cd server && composer install

or

cd server && php composer.phar install
```

You also need to create the environment variables - at the server folder root - to init the database. To create the .env file just copy and fill the informations as the **_.env.example_** file and create the database following the Schema in the database folder or create a new Schema and make necessary code adaptations. To download and update the product informations just update the code for use with your plataform. This project was made using the VTEX plataform, so it's necessary create the files: *ean.txt* and *sku.txt* to obtain the relevant information about the products.

### Development

Start the PHP server. Just follow the instructions of the chosen server to start the development server, but if you are using the PHP Built-in web server, use the following command to start the server:

> composer server

With the server started it'll now be possible to access the available products via the */products* route. The allowed params are:

- *limit* - Response limit
- *page* - Current ordered page
- *productName* - Product name searched
- *discountTag* - Discount limit (Only one value is allowed)
- *productCategories* - Product categories (Allowed more than one category separated by the plus (+) signal)
- *productID* - Product ID
- *sku* - Product SKU
- *bestPrice* - Price range (Allowed two values separated by the plus signal(+)). _Obs.: The value must not have commas or periods. PS.: $ 30,25 must be passed as 3025_

### Products

Products are identify by productID and also by sku. All products have the porperties:

Field                | Type					| Description
-------------------- | ---------------------|-------------------------
productID            | integer				| Unique ID of product
sku                  | integer				| Product SKU - Unique identifier together productID (usually is the same value that the productID)
sellerID             | string 				| Seller identifier (Default is 1)
imageUrl             | string				| Product image url
detailUrl            | string				| Product page url
productName          | string				| Product name
discountTag          | string				| Discount tag based on the price (on/off)
listPrice            | integer				| List price
bestPrice            | integer				| Discounted amount 
productCategories    | string				| Categories list

## Use

To list or filter the products just use the _**/products**_ passing all necessary queryStrings.

_PS.: The product limit default per page is 10 and the max is 100 and also the current page is always the first_

Some filters and search examples:

- _**/products**_ : Return all products in the first page with default limit of 10 products
- _**/products?productName=some%20%product**_ : Return all products with name like the informade
- _**/products?discountTag=40**_ : Returns products with the requested discount tag following the following logic (range between discount-10 and discount)
- _**/products?productCategories=Emagrecimento+beleza+saude**_ = Return all products that belong to the informed category
- _**/products?productCategories=Emagrecimento+beleza+saude&limit=20**_ : Limit the result of items per page
- _**/products?productCategories=Emagrecimento+beleza+saude&page=2**_ : Get the page 2
- _**/products?productCategories=Emagrecimento&discountTag=30&limit=15&page=2**_ : Filters uniom
- _**/products?bestPrice=3000+4000**_ : Filter by the price range

_The limit and page params can be sent together another filter allowed and, when omitted, they assume the default values of 10 e 1, respectively_

### Response example

When requesting the url _**/products?productCategories=Emagrecimento+beleza+saude&limit=2**_, it'll return like that:

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

### Initial download of product information

To start the product information download it's necessary execute the file _initDownload.php_ by web browser or PHP CLI. Using PHP CLI just execute the command:

>php initDownload.php

The script runs around cycle of 1000 request and stop by 20 seconds to don't overflow the reqest limits of used APIS.

_Some products can return without discount tag, price e discount informatons_

### Products update

The update process is like the download process. You just need start the file _initUpdate.php_ by web browser or PHP CLI. Using PHP CLI just execute:

> php initUpdate.php

## Technologies and tools

### Server

- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Composer](https://getcomposer.org/)
- [Insomnia](https://insomnia.rest/download)

### Web

- [Next.js](https://nextjs.org/)
- [styled-components](https://styled-components.com/)

### Mobile

- [React Native](https://reactnative.dev/)
- [styled-components](https://styled-components.com/)

## Author

- [Alessandro Miranda](https://github.com/Alessandro-Miranda) - _Initial idea and development_

## License

This project is under the MIT license - see the file [LICENSE](https://github.com/Alessandro-Miranda/pagina-produtos/blob/main/LICENSE) to more details.