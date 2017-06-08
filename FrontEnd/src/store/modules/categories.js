import * as categories from '@/services/categories'

const state = {
  id: [],
  data: {},
}

const mutations = {
  /**
   * 更新类别信息
   * @param  {Object} payload 类别数据
   */
  updateCategories(state, payload) {
    state.id = payload.map(item => item.id)

    const data = {}

    payload.forEach((item) => {
      data[item.id] = item
    })

    state.data = data
  },
}

const actions = {
  /**
   * 获取最新类别信息
   * @return {Promise}
   */
  fetchCategories(context) {
    return categories.fetchCategories().then((response) => {
      context.commit('updateCategories', response.data)

      return Promise.resolve(response.data)
    }).catch(err => Promise.reject(err.response.data))
  },
}

export default {
  state,
  mutations,
  actions,
}
