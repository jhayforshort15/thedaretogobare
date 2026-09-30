import brands from './brands'
import categories from './categories'
import fights from './fights'
import orders from './orders'
import posts from './posts'
import products from './products'

const resources = {
    brands: Object.assign(brands, brands),
    categories: Object.assign(categories, categories),
    fights: Object.assign(fights, fights),
    orders: Object.assign(orders, orders),
    posts: Object.assign(posts, posts),
    products: Object.assign(products, products),
}

export default resources