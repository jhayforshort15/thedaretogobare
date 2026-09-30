import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/fights',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\ListFights::__invoke
* @see app/Filament/Resources/Fights/Pages/ListFights.php:7
* @route '/admin/fights'
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
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/fights/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\CreateFight::__invoke
* @see app/Filament/Resources/Fights/Pages/CreateFight.php:7
* @route '/admin/fights/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
export const edit = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/fights/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
edit.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return edit.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
edit.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
edit.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
const editForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
editForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
editForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

const fights = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit),
}

export default fights