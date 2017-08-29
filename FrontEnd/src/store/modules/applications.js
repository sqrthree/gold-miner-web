import * as applications from '@/services/applications'

const state = {
  texts: {
    id: [],
    data: {},
  },
  applicants: {
    id: [],
    data: {},
  },
}

const getters = {
  applicants(state) {
    return state.applicants.id.map(item => state.applicants.data[item])
  },
  texts(state) {
    return state.texts.id.map(item => state.texts.data[item])
  },
}

const mutations = {
  /**
   * 将试译的英文稿进行存储
   * @param {Array} payload 试译的英文稿数据
   */
  initTexts(state, payload) {
    state.texts.id = payload.map(item => item.id)
    const data = {}

    payload.forEach((item) => {
      data[item.id] = item
    })

    state.texts.data = data
  },

  /**
   * 添加一个新的试译文稿
   * @param {Object} payload 试译的英文稿数据
   */
  addText(state, payload) {
    state.texts.id.unshift(payload.id)
    state.texts.data[payload.id] = payload
  },

  /**
   * 更新一个试译文稿
   * @param {Object} payload 试译的英文稿数据
   */
  updateText(state, payload) {
    state.texts.data[payload.id] = payload
  },

  /**
   * 删除一个试译文稿
   * @param  {Number} id    要删除的文本 ID
   */
  removeText(state, id) {
    const index = state.texts.id.indexOf(id)

    if (index === -1) return

    state.texts.id.splice(index, 1)
  },

  /**
   * 将所有的译者申请信息进行存储
   * @param  {Array} payload 译者申请信息
   */
  initApplicants(state, payload) {
    state.applicants.id = payload.map(item => item.id)
    const data = {}

    payload.forEach((item) => {
      data[item.id] = item
    })

    state.applicants.data = data
  },

  /**
   * 移除对应的译者申请数据中
   * @param  {Number} payload 译者申请 id
   */
  removeApplicant(state, id) {
    const index = state.applicants.id.indexOf(id)

    if (index === -1) return

    state.applicants.id.splice(index, 1)
  },
}

const actions = {
  /**
   * 获取申请者列表
   * @return {Promise}
   */
  fetchApplicants(context) {
    return applications.fetchApplicants().then((response) => {
      context.commit('initApplicants', response)

      return Promise.resolve(response)
    })
  },

  /**
   * 提交翻译的译文和最终数据
   * @param  {Object} payload 申请信息和翻译结果
   * @return {Promise}
   */
  submitApplication(context, payload) {
    return applications.submitApplication(payload).then(response => Promise.resolve(response))
  },

  /**
   * 针对译者申请提出审核意见
   * @param  {Object} payload 审核意见
   * @return {Promise}
   */
  submitOpinionOfApplications(context, payload) {
    return applications.submitOpinion(payload).then((response) => {
      context.commit('removeApplicant', payload.id)
      return Promise.resolve(response)
    })
  },

  /**
   * 获取随机的一个试译文本
   * @param  {String} payload 指定要获取的类别（可选）
   * @return {Promise}
   */
  fetchRandomText(context, payload) {
    return applications.fetchRandomText(payload)
  },

  /**
   * 获取所有的试译文本
   * @param  {Array} payload 指定要获取的类别
   * @return {Promise}
   */
  fetchTexts(context, payload) {
    return applications.fetchTexts(payload).then((response) => {
      context.commit('initTexts', response)
      return Promise.resolve(response)
    })
  },

  /**
   * 创建新试译文本
   * @param  {Object} payload 新试译文本的一些信息
   * @return {Promise}
   */
  addText(context, payload) {
    return applications.addText(payload).then((response) => {
      context.commit('addText', response)
      return Promise.resolve(response)
    })
  },

  /**
   * 更新试译文本
   * @param  {Object} payload 试译文本的一些信息
   * @return {Promise}
   */
  updateText(context, payload) {
    return applications.updateText(payload).then((response) => {
      context.commit('updateText', response)
      return Promise.resolve(response)
    })
  },

  /**
   * 删除试译文本
   * @param  {Number} id      要删除的文本 ID
   */
  deleteText(context, id) {
    return applications.deleteText(id).then((response) => {
      context.commit('removeText', id)
      return Promise.resolve(response)
    })
  },
}

export default {
  state,
  getters,
  mutations,
  actions,
}
