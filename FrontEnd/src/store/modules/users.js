import assign from 'lodash/assign'
import * as user from '@/services/users'

const state = {
  oAuth: {
    logIn: false,
    token: '',
    id: '',
  },
  id: [],
  data: {},
}

const getters = {
  logIn: state => state.oAuth.logIn,
  currentUser: state => state.data[state.oAuth.id] || {},
}

const mutations = {
  /**
   * 用户登录后，将其标记为登录状态
   * @param {Object} payload 登录的用户信息
   */
  login(state, payload) {
    if (!state.data[payload.id]) {
      state.id.push(payload.id)
      state.data[payload.id] = {}
    }

    state.oAuth.logIn = true
    state.oAuth.token = payload.token
    state.oAuth.id = payload.id

    assign(state.data[payload.id], payload)
  },

  /**
   * 用户退出后，将其标记为未登录状态
   * @param {Object} payload 用户信息
   */
  logout(state) {
    assign(state.oAuth, {
      logIn: false,
      token: '',
      id: '',
    })
  },

  /**
   * 将用户信息更新至 store
   * @param {Object} payload 需要更新的用户信息
   */
  updateUserInfo(state, payload) {
    state.data = assign({}, state.data, {
      [payload.id]: payload,
    })
  },
}

const actions = {
  /**
   * 验证邀请码是否有效
   * @param  {String} invitationCode 邀请码
   * @return {Promise}
   */
  validateInvitationCode(context, payload) {
    context.commit('showLoading')
    context.commit('setLoadingText', '我们正在为您开辟专享通道，请稍后。')

    return user.validateInvitationCode(payload).then((response) => {
      context.commit('hideLoading')
      return Promise.resolve(response)
    }).catch((err) => {
      context.commit('hideLoading')
      return Promise.reject(err)
    })
  },

  /**
   * 退出
   * @return {Promise}
   */
  logout(context) {
    context.commit('showLoading')

    return user.logout().then(() => {
      context.commit('logout')
      context.commit('hideLoading')
    })
  },

  /**
   * 将用户信息更新至 store
   * @param  {Number} id 需要更新的用户 id
   * @return Promise
   */
  fetchUserInfo(context, id) {
    return user.fetchUserInfo(id).then((data) => {
      context.commit('updateUserInfo', data)
      return data
    })
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
