import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
const EditFight = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditFight.url(args, options),
    method: 'get',
})

EditFight.definition = {
    methods: ["get","head"],
    url: '/admin/fights/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
EditFight.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditFight.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
EditFight.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditFight.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
EditFight.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditFight.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
const EditFightForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditFight.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
EditFightForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditFight.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Fights\Pages\EditFight::__invoke
* @see app/Filament/Resources/Fights/Pages/EditFight.php:7
* @route '/admin/fights/{record}/edit'
*/
EditFightForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditFight.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EditFight.form = EditFightForm

export default EditFight