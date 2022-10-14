<?php

if (! function_exists('getStoreRequest')) {
    /**
     * Identifica qual loja fez a requisição.
     * Se for 'null' é requisição do seller center.
     *
     * @return  int|null
     */
    function getStoreRequest(): int|null
    {
        $store = request()->route('store');

        if ($store === 'null') {
            return null;
        }

        return (int)$store;
    }
}

if (! function_exists('getTenantRequest')) {
    /**
     * Identifica qual o tenant que fez a requisição.
     *
     * @return  string
     */
    function getTenantRequest(): string
    {
        return request()->route('tenant');
    }
}

if (! function_exists('getAllPramsRequest')) {
    /**
     * Recupera os parâmetros da requisição.
     *
     * @return  array
     */
    function getAllPramsRequest(): array
    {
        return [
            'tenant' => getTenantRequest(),
            'store'  => getStoreRequest()
        ];
    }
}