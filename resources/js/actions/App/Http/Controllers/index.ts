import HomeController from './HomeController'
import ShopController from './ShopController'
import Settings from './Settings'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    ShopController: Object.assign(ShopController, ShopController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers