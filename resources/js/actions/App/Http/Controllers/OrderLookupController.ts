import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
export const form = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: form.url(options),
    method: 'get',
})

form.definition = {
    methods: ["get","head"],
    url: '/order-lookup',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
form.url = (options?: RouteQueryOptions) => {
    return form.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
form.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: form.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
form.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: form.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
const formForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
formForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrderLookupController::form
* @see app/Http/Controllers/OrderLookupController.php:13
* @route '/order-lookup'
*/
formForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: form.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

form.form = formForm

/**
* @see \App\Http\Controllers\OrderLookupController::find
* @see app/Http/Controllers/OrderLookupController.php:20
* @route '/order-lookup'
*/
export const find = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: find.url(options),
    method: 'post',
})

find.definition = {
    methods: ["post"],
    url: '/order-lookup',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrderLookupController::find
* @see app/Http/Controllers/OrderLookupController.php:20
* @route '/order-lookup'
*/
find.url = (options?: RouteQueryOptions) => {
    return find.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderLookupController::find
* @see app/Http/Controllers/OrderLookupController.php:20
* @route '/order-lookup'
*/
find.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: find.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderLookupController::find
* @see app/Http/Controllers/OrderLookupController.php:20
* @route '/order-lookup'
*/
const findForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: find.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderLookupController::find
* @see app/Http/Controllers/OrderLookupController.php:20
* @route '/order-lookup'
*/
findForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: find.url(options),
    method: 'post',
})

find.form = findForm

const OrderLookupController = { form, find }

export default OrderLookupController