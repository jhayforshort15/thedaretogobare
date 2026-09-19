import Brands from './Brands'
import Categories from './Categories'
import Orders from './Orders'
import Products from './Products'

const Resources = {
    Brands: Object.assign(Brands, Brands),
    Categories: Object.assign(Categories, Categories),
    Orders: Object.assign(Orders, Orders),
    Products: Object.assign(Products, Products),
}

export default Resources