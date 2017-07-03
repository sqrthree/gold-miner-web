import Article from './Article'
import Navbar from './Navbar'
import Footer from './Footer'
import Popover from './Popover'
import Card from './Card'
import RankingList from './RankingList'

const components = [
  Article,
  Navbar,
  Footer,
  Popover,
  Card,
  RankingList,
]

const install = function install(Vue) {
  if (install.installed) return

  components.forEach((component) => {
    Vue.component(component.name, component)
  })
}

export default {
  install,
}
