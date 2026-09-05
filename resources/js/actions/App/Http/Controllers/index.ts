import HomeController from './HomeController'
import Settings from './Settings'

const Controllers = {
    HomeController: Object.assign(HomeController, HomeController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers