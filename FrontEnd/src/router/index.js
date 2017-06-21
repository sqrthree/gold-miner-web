import Vue from 'vue'
import Router from 'vue-router'
import nprogress from 'nprogress'
import { Message } from 'element-ui'

import Index from '@/containers/Index'
import Recommends from '@/containers/Recommends'
import Applications from '@/containers/Applications'
import Auth from '@/containers/Auth'
import Articles from '@/containers/Articles'
import store from '@/store'

const localStorage = require('store')

Vue.use(Router)

const rules = {
  loginRequired() {
    if (store.getters.logIn) {
      return true
    }

    const user = localStorage.get('user') || {}

    if (user.token) {
      return true
    }

    return '登录以后再来尝试吧'
  },

  adminRequired() {
    const user = store.getters.currentUser || localStorage.get('user')

    if (user.isadmin) {
      return true
    }

    return '小子，你的权限不足呐'
  },
}

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index,
      meta: {
        title: '掘金翻译计划',
      },
    },
    {
      path: '/recommends',
      name: 'Recommends',
      component: Recommends.New,
      meta: {
        title: '推荐文章',
      },
    },
    {
      path: '/recommends/:id',
      name: 'RecommendsDetail',
      component: Recommends.Detail,
      meta: {
        title: '推荐文章详情',
        rules: [
          'loginRequired',
        ],
      },
    },
    {
      path: '/applications',
      component: Applications.Base,
      meta: {
        title: '加入我们',
      },
      children: [
        {
          path: 'apply',
          name: 'Apply',
          component: Applications.Apply,
          meta: {
            title: '加入我们',
          },
        },
        {
          path: 'applicants',
          name: 'applicants',
          component: Applications.Applicants,
          meta: {
            title: '译者申请列表',
            rules: [
              'loginRequired',
              'adminRequired',
            ],
          },
        },
        {
          path: 'applicants/:id',
          name: 'applicant',
          component: Applications.Applicant,
          meta: {
            title: '译者申请',
            rules: [
              'loginRequired',
              'adminRequired',
            ],
          },
        },
        {
          path: 'texts',
          name: 'TextsForApplication',
          component: Applications.Texts,
          meta: {
            title: '试译文本列表',
            rules: [
              'loginRequired',
              'adminRequired',
            ],
          },
        },
      ],
    },
    {
      path: '/auth',
      name: 'Auth',
      component: Auth.Base,
      children: [
        {
          path: 'logout',
          name: 'Logout',
          component: Auth.Logout,
        },
      ],
    },
    {
      path: '/articles',
      component: Articles.Base,
      children: [
        {
          path: '',
          name: 'ArticleList',
          component: Articles.List,
          meta: {
            title: '文章列表',
          },
        },
        {
          path: ':id',
          name: 'ArticleItem',
          component: Articles.Item,
        },
        {
          path: ':id/details',
          name: 'ArticleDetail',
          component: Articles.Detail,
        },
      ],
    },
  ],
})

router.beforeEach((to, from, next) => {
  if (!to.meta.rules) return next()

  const middlewares = to.meta.rules.map(item => rules[item])

  for (let i = 0; i < middlewares.length; i += 1) {
    const result = middlewares[i](to)

    if (result !== true) {
      Message({ type: 'error', message: result || '出现错误啦' })

      return next('/')
    }
  }

  return next()
})

router.afterEach((to) => {
  nprogress.done()
  document.title = to.meta.title || '掘金翻译计划'
})

export default router
