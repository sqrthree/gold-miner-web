import axios from './axios'

export const fetchArticles = function fetchArticles(options) {
  return axios.get('/api/articles', {
    params: options,
  }).then(response => response.data)
}

export const test = function test() {

}
