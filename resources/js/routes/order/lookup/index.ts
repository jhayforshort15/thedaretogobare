import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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

const lookup = {
    find: Object.assign(find, find),
}

export default lookup