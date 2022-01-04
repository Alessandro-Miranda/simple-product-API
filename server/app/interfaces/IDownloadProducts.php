<?php

interface IDownloadProducts
{
    /**
     * Obtém as informações sobre preço (De/por) dos produtos com base no sku
     *
     * @return self
     */
    public function getPriceInformations(): self;

    /**
     * Obtém a url da imagem, url da página de destino, categorias, id e nome do produto com base no EAN
     *
     * @return self
     */
    public function getProductInformations(): self;

    /**
     * Invoca a inserção dos produtos no banco de dados
     *
     * @return void
     */
    public function saveProducts(): void;
}