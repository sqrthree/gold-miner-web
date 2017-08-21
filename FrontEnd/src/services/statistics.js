import axios from './axios'

export const fetchOverview = function fetchOverview() {
  return axios.get('/api/statistics/overview')
}

export const fetchRanks = function fetchRanks() {
  return axios.get('/api/statistics')
}
