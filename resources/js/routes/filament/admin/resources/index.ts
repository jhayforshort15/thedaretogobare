import brands from './brands'
import categories from './categories'
import orders from './orders'
import products from './products'

const resources = {
    brands: Object.assign(brands, brands),
    categories: Object.assign(categories, categories),
    orders: Object.assign(orders, orders),
    products: Object.assign(products, products),
}

export default resources