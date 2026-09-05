import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
const ListBrands = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBrands.url(options),
    method: 'get',
})

ListBrands.definition = {
    methods: ["get","head"],
    url: '/admin/brands',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
ListBrands.url = (options?: RouteQueryOptions) => {
    return ListBrands.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
ListBrands.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBrands.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
ListBrands.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListBrands.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
const ListBrandsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBrands.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
ListBrandsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBrands.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Brands\Pages\ListBrands::__invoke
* @see app/Filament/Resources/Brands/Pages/ListBrands.php:7
* @route '/admin/brands'
*/
ListBrandsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBrands.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListBrands.form = ListBrandsForm

export default ListBrands