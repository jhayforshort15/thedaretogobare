import HomeController from './HomeController'
import ShopController from './ShopController'
import CartController from './CartController'
import Settings from './Settings'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    ShopController: Object.assign(ShopController, ShopController),
    CartController: Object.assign(CartController, CartController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers