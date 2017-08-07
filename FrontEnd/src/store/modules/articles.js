import union from 'lodash/union'
import assign from 'lodash/assign'
import * as articles from '@/services/articles'

const state = {
  data: {},
  lastest: {
    page: 0,
    list: [],
  },
  waiting: {
    page: 0,
    list: [],
  },
  doing: {
    page: 0,
    list: [],
  },
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
      state[payload.type].list = list
    } else {
      state[payload.type].list = union(state[payload.type].list, list)
    }

    state[payload.type].page = payload.page
  },
}

const actions = {
  fetchArticles(context, payload) {
    return articles.fetchArticles({
      status: payload.type,
      page: payload.page,
      perpage: payload.perpage || 10,
    }).then((data) => {
      context.commit('setArticles', {
        type: payload.type,
        page: payload.page,
        data,
      })
    })
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
