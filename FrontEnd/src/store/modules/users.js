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
  login(state, payload) {
    if (!state.data[payload.id]) {
      state.id.push(payload.id)
      state.data[payload.id] = {}
    }

    state.oAuth.logIn = true
    state.oAuth.token = payload.token
    state.oAuth.id = payload.id

    Object.assign(state.data[payload.id], payload)
  },
  logout(state) {
    Object.assign(state.oAuth, {
      logIn: false,
      token: '',
      id: '',
    })
  },
}

const actions = {
  /**
   * 验证邀请码是否有效
   * @param  {String} invitationCode 邀请码
   * @return {Promise}
   */
  validateInvitationCode(context, invitationCode) {
    context.commit('showLoading')
    context.commit('setLoadingText', '我们正在为您开辟专享通道，请稍后。')

    return user.validateInvitationCode(invitationCode).then((response) => {
      context.commit('hideLoading')
      return Promise.resolve(response.data)
    }).catch((err) => {
      context.commit('hideLoading')
      return Promise.reject(err.response.data)
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
}

export default {
  state,
  getters,
  mutations,
  actions,
}
