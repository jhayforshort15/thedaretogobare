import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
const EditBrand = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditBrand.url(args, options),
    method: 'get',
})

EditBrand.definition = {
    methods: ["get","head"],
    url: '/admin/brands/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
EditBrand.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditBrand.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
EditBrand.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditBrand.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
EditBrand.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditBrand.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
const EditBrandForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBrand.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
EditBrandForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBrand.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\EditBrand::__invoke
* @see app/Filament/Resources/Brands/Pages/EditBrand.php:7
* @route '/admin/brands/{record}/edit'
*/
EditBrandForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBrand.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EditBrand.form = EditBrandForm

export default EditBrand