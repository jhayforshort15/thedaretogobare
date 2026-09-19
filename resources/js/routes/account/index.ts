import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
export const orders = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

orders.definition = {
    methods: ["get","head"],
    url: '/account/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
orders.url = (options?: RouteQueryOptions) => {
    return orders.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
orders.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
orders.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orders.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
const ordersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
ordersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::orders
* @see app/Http/Controllers/AccountOrderController.php:12
* @route '/account/orders'
*/
ordersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orders.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

orders.form = ordersForm

const account = {
    orders: Object.assign(orders, orders),
}

export default account