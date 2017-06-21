import * as user from '@/services/users'

const state = {
  applicants: [],
  recommends: [],
  system: [],
  total: 0,
}

const getters = {

}

const mutations = {
  /**
   * 更新当前用户的通知消息内容
   */
  updateNotifications(state, payload) {
    Object.assign(state, payload)
  },
}

const actions = {
  /**
   * 获取当前用户的通知消息
   * @return {Promise}
   */
  fetchNotifications(context) {
    return user.fetchNotifications().then((response) => {
      response.data.applicants.map(item => Object.assign(item, {
        title: '申请译者',
        link: `/applications/applicants/${item.id}`,
      }))

      response.data.recommends.map((item) => {
        item.link = `/recommends/${item.id}`

        return item
      })

      context.commit('updateNotifications', response.data)

      return Promise.resolve(response.data)
    }).catch(err => Promise.reject(err.response.data))
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
