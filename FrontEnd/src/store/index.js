import Vue from 'vue'
import Vuex from 'vuex'

import modules from './modules'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules,
})

if (module.hot) {
  // 使 actions 和 mutations 成为可热重载模块
  module.hot.accept(['./modules/users'], () => {
    /* eslint global-require: off */
    const newModule = require('./modules').default
    // 加载新模块
    store.hotUpdate({
      modules: newModule,
    })
  })
}

export default store
