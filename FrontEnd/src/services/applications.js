import axios from './axios'

export const fetchRandomText = function fetchRandomText(category) {
  return axios.get(`/api/articles/random/${category}`)
}

export const fetchTexts = function fetchTexts(options) {
  return axios.get('/api/articles', { params: options })
}

export const addText = function addText(options) {
  return axios.post('/api/articles', options)
}

export const updateText = function updateText(options) {
  return axios.put(`/api/articles/${options.id}`, options)
}

export const deleteText = function deleteText(id) {
  return axios.delete(`/api/articles/${id}`)
}

export const fetchApplicants = function fetchApplicants() {
  return axios.get('/api/applicants')
}

export const submitApplication = function submitRequest(options) {
  return axios.post('/api/applicants', options)
}

export const submitOpinion = function submitOpinion(options) {
  return axios.patch(`/api/applicants/${options.id}`, options)
}
