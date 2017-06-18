import * as recommends from '@/services/recommends'

const state = {
  id: [],
  data: {},
}

const mutations = {
  /**
   * 添加推荐的文章
   * @param {Object} payload 添加的文章数据
   */
  addRecommend(state, payload) {
    state.id.push(payload.id)
    state.data[payload.id] = payload
  },
}

const actions = {
  /**
   * 添加推荐的文章
   * @param {Object} payload 添加的文章数据
   */
  addRecommend(context, payload) {
    return recommends.addRecommend(payload).then((response) => {
      context.commit('addRecommend', response.data)

      return Promise.resolve(response.data)
    }).catch(err => Promise.reject(err.response.data))
  },
}

export default {
  state,
  mutations,
  actions,
}
