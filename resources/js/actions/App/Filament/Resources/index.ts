import Brands from './Brands'
import Categories from './Categories'
import Fights from './Fights'
import Orders from './Orders'
import Posts from './Posts'
import Products from './Products'

const Resources = {
    Brands: Object.assign(Brands, Brands),
    Categories: Object.assign(Categories, Categories),
    Fights: Object.assign(Fights, Fights),
    Orders: Object.assign(Orders, Orders),
    Posts: Object.assign(Posts, Posts),
    Products: Object.assign(Products, Products),
}

export default Resources