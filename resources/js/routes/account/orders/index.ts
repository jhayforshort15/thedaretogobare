import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
export const show = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/account/orders/{orderNumber}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
show.url = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { orderNumber: args }
    }

    if (Array.isArray(args)) {
        args = {
            orderNumber: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        orderNumber: args.orderNumber,
    }

    return show.definition.url
            .replace('{orderNumber}', parsedArgs.orderNumber.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
show.get = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
show.head = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
const showForm = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
showForm.get = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountOrderController::show
* @see app/Http/Controllers/AccountOrderController.php:32
* @route '/account/orders/{orderNumber}'
*/
showForm.head = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const orders = {
    show: Object.assign(show, show),
}

export default orders