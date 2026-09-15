import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/checkout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::index
* @see app/Http/Controllers/CheckoutController.php:22
* @route '/checkout'
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

/**
* @see \App\Http\Controllers\CheckoutController::store
* @see app/Http/Controllers/CheckoutController.php:47
* @route '/checkout'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CheckoutController::store
* @see app/Http/Controllers/CheckoutController.php:47
* @route '/checkout'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::store
* @see app/Http/Controllers/CheckoutController.php:47
* @route '/checkout'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CheckoutController::store
* @see app/Http/Controllers/CheckoutController.php:47
* @route '/checkout'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CheckoutController::store
* @see app/Http/Controllers/CheckoutController.php:47
* @route '/checkout'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
export const confirmation = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(args, options),
    method: 'get',
})

confirmation.definition = {
    methods: ["get","head"],
    url: '/checkout/confirmation/{orderNumber}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
confirmation.url = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return confirmation.definition.url
            .replace('{orderNumber}', parsedArgs.orderNumber.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
confirmation.get = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
confirmation.head = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmation.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
const confirmationForm = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
confirmationForm.get = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::confirmation
* @see app/Http/Controllers/CheckoutController.php:120
* @route '/checkout/confirmation/{orderNumber}'
*/
confirmationForm.head = (args: { orderNumber: string | number } | [orderNumber: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

confirmation.form = confirmationForm

const checkout = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    confirmation: Object.assign(confirmation, confirmation),
}

export default checkout