import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PrintifyWebhookController::webhook
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/printify/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PrintifyWebhookController::webhook
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintifyWebhookController::webhook
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PrintifyWebhookController::webhook
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PrintifyWebhookController::webhook
* @see app/Http/Controllers/PrintifyWebhookController.php:12
* @route '/printify/webhook'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

const printify = {
    webhook: Object.assign(webhook, webhook),
}

export default printify