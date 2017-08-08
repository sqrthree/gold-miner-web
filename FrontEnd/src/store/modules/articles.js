import union from 'lodash/union'
import assign from 'lodash/assign'
import * as articles from '@/services/articles'

const state = {
  data: {},
  posted: [],
  awaiting: [],
  progressing: [],
}

const getters = {

}

const mutations = {
  setArticles(state, payload) {
    const data = {}
    const list = []

    payload.data.forEach((item) => {
      data[item.id] = item
      list.push(item.id)
    })

    state.data = assign({}, state.data, data)

    if (payload.page === 1) {
      state[payload.type] = list
    } else {
      state[payload.type] = union(state[payload.type], list)
    }
  },
}

const actions = {
  fetchArticles(context, payload) {
    return articles.fetchArticles(payload.type, {
      page: payload.page,
      perpage: payload.perpage || 10,
    }).then((data) => {
      context.commit('setArticles', {
        type: payload.type,
        page: payload.page,
        data,
      })

      return Promise.resolve(data)
    })
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
