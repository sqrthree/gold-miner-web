import * as articles from '@/services/articles'

const state = {
  lastest: {
    page: 0,
    data: [],
  },
  waiting: {
    page: 0,
    data: [],
  },
  doing: {
    page: 0,
    data: [],
  },
}

const getters = {

}

const mutations = {
  setArticles(state, payload) {
    state[payload.type].data = payload.data
  },
  updatePageNumber(state, payload) {
    state[payload.type].page += 1
  },
}

const actions = {
  fetchArticles(context, payload) {
    context.commit('updatePageNumber', { type: payload.type })
    context.commit('showLoading')

    return articles.fetchArticles({
      status: payload.type,
      page: context.state[payload.type].page,
      perpage: payload.perpage || 10,
    }).then((data) => {
      context.commit('hideLoading')
      context.commit('setArticles', {
        type: payload.type,
        data,
      })
    }).catch((err) => {
      context.commit('hideLoading')
      return Promise.reject(err.response.data)
    })
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
