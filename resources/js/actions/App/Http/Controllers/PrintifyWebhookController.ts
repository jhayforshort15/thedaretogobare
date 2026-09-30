import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PrintifyWebhookController::handle
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
export const handle = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

handle.definition = {
    methods: ["post"],
    url: '/printify/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PrintifyWebhookController::handle
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
handle.url = (options?: RouteQueryOptions) => {
    return handle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintifyWebhookController::handle
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
handle.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PrintifyWebhookController::handle
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
const handleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PrintifyWebhookController::handle
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
handleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handle.url(options),
    method: 'post',
})

handle.form = handleForm

const PrintifyWebhookController = { handle }

export default PrintifyWebhookController