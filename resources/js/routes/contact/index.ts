import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
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

const contact = {
    submit: Object.assign(submit, submit),
}

export default contact