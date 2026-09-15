import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ExpressCheckoutController::intent
* @see app/Http/Controllers/ExpressCheckoutController.php:25
* @route '/express/intent'
*/
export const intent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: intent.url(options),
    method: 'post',
})

intent.definition = {
    methods: ["post"],
    url: '/express/intent',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ExpressCheckoutController::intent
* @see app/Http/Controllers/ExpressCheckoutController.php:25
* @route '/express/intent'
*/
intent.url = (options?: RouteQueryOptions) => {
    return intent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpressCheckoutController::intent
* @see app/Http/Controllers/ExpressCheckoutController.php:25
* @route '/express/intent'
*/
intent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: intent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpressCheckoutController::intent
* @see app/Http/Controllers/ExpressCheckoutController.php:25
* @route '/express/intent'
*/
const intentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: intent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpressCheckoutController::intent
* @see app/Http/Controllers/ExpressCheckoutController.php:25
* @route '/express/intent'
*/
intentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: intent.url(options),
    method: 'post',
})

intent.form = intentForm

const ExpressCheckoutController = { intent }

export default ExpressCheckoutController