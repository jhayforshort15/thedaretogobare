import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
const ListFights = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListFights.url(options),
    method: 'get',
})

ListFights.definition = {
    methods: ["get","head"],
    url: '/admin/fights',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
ListFights.url = (options?: RouteQueryOptions) => {
    return ListFights.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
ListFights.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListFights.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
ListFights.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListFights.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
const ListFightsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListFights.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
ListFightsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListFights.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
ListFightsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListFights.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListFights.form = ListFightsForm

export default ListFights