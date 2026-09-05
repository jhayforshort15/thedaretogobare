import brands from './brands'
import categories from './categories'
import products from './products'

const resources = {
    brands: Object.assign(brands, brands),
    categories: Object.assign(categories, categories),
    products: Object.assign(products, products),
}

export default resources