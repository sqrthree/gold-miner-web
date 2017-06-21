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

  /**
   * 更新推荐文章列表
   * @param {Object} payload 最新推荐文章列表数据
   */
  updateRecommend(state, payload) {
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
   * 添加推荐的文章
   * @param {Object} payload 添加的文章数据
   */
  addRecommend(context, payload) {
    return recommends.addRecommend(payload).then((response) => {
      context.commit('addRecommend', response.data)

      return Promise.resolve(response.data)
    }).catch(err => Promise.reject(err.response.data))
  },

  /**
   * 获取所有的推荐文章列表
   * @return {Promise}
   */
  fetchRecommends(context) {
    return recommends.fetchRecommends().then((response) => {
      context.commit('updateRecommend', response.data)

      return Promise.resolve(response.data)
    }).catch(err => Promise.reject(err.response.data))
  },

  /**
   * 提交审核意见
   * @param  {Object} payload 意见内容
   * @return {Promise}
   */
  submitOpinionOfRecommends(context, payload) {
    return recommends.submitOpinion(payload)
      .then(response => Promise.resolve(response.data))
      .catch(err => Promise.reject(err.response.data))
  },
}

export default {
  state,
  mutations,
  actions,
}
