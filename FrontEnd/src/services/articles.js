import axios from './axios'

export const fetchArticles = function fetchArticles(options) {
  return axios.get('/api/translations', {
    params: options,
  })
}

export const fetchArticleWithId = function fetchArticleWithId(id) {
  return axios.get(`/api/translations/${id}`)
}

export const updateArticleWithId = function updateArticleWithId(id, data) {
  return axios.post(`/api/translations/${id}`, data)
}

export const claimTranslation = function claimTranslation(options) {
  return axios.post('/api/translations/claim/translation', options)
}

export const claimReview = function claimReview(options) {
  return axios.post('/api/translations/claim/review', options)
}
