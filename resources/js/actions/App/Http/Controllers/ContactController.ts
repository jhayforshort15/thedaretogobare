import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/contact-us',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:15
* @route '/contact-us'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\ContactController::submit
* @see app/Http/Controllers/ContactController.php:27
* @route '/contact-us'
*/
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/contact-us',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactController::submit
* @see app/Http/Controllers/ContactController.php:27
* @route '/contact-us'
*/
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::submit
* @see app/Http/Controllers/ContactController.php:27
* @route '/contact-us'
*/
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactController::submit
* @see app/Http/Controllers/ContactController.php:27
* @route '/contact-us'
*/
const submitForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactController::submit
* @see app/Http/Controllers/ContactController.php:27
* @route '/contact-us'
*/
submitForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(options),
    method: 'post',
})

submit.form = submitForm

const ContactController = { show, submit }

export default ContactController