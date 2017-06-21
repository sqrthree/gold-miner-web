import axios from 'axios'
import nprogress from 'nprogress'

const store = require('store')

const instance = axios.create()

instance.interceptors.request.use((config) => {
  const { token } = store.get('user') || {}

  config.headers.Authorization = token

  nprogress.start()

  return config
}, (error) => {
  nprogress.done()
  return Promise.reject(error)
})

instance.interceptors.response.use((response) => {
  nprogress.done()
  return response
}, (error) => {
  nprogress.done()
  return Promise.reject(error)
})

export default instance
