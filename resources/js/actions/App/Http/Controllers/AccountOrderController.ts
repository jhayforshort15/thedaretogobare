import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/account/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::index
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const AccountOrderController = { index }

export default AccountOrderController