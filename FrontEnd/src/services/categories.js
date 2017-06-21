import axios from './axios'

export const fetchCategories = function fetchCategories() {
  return axios.get('/api/categories')
}

export const addCategory = function addCategory(category) {
  return axios.post('/api/categories', { category })
}
