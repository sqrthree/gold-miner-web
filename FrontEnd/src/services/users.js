import axios from './axios'

export const logout = function logout() {
  return axios.get('/auth/logout')
}

export const fetchUserInfo = function fetchUserInfo(id) {
  return axios.get(`/api/users/${id}`)
}

export const validateInvitationCode = function validateInvitationCode(payload) {
  return axios.post('/api/applicants/check', payload)
}

export const fetchNotifications = function fetchNotifications() {
  return axios.get('/api/notifications')
}

export const fetchSettings = function fetchSettings(id) {
  return axios.get(`/api/UserSettings/${id}`)
}

export const updateSettings = function fetchSettings(id, payload) {
  return axios.post(`/api/UserSettings/${id}`, payload)
}
