import HomeController from './HomeController'
import ShopController from './ShopController'
import CartController from './CartController'
import CheckoutController from './CheckoutController'
import StripeWebhookController from './StripeWebhookController'
import Settings from './Settings'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    ShopController: Object.assign(ShopController, ShopController),
    CartController: Object.assign(CartController, CartController),
    CheckoutController: Object.assign(CheckoutController, CheckoutController),
    StripeWebhookController: Object.assign(StripeWebhookController, StripeWebhookController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers