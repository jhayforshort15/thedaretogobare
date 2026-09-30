import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
const CreateFight = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateFight.url(options),
    method: 'get',
})

CreateFight.definition = {
    methods: ["get","head"],
    url: '/admin/fights/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
CreateFight.url = (options?: RouteQueryOptions) => {
    return CreateFight.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
CreateFight.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateFight.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
CreateFight.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateFight.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
const CreateFightForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateFight.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
CreateFightForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateFight.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
CreateFightForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateFight.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateFight.form = CreateFightForm

export default CreateFight